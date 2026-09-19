<?php

namespace App\Http\Controllers;

use App\Facturacion\Application\DTO\MapeadorFacturaErp;
use App\Facturacion\Application\Services\EmitirComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico;
use App\Facturacion\Infrastructure\Providers\Peru\ConstructorUblGreenter;
use App\Facturacion\Infrastructure\Providers\Peru\ResolverConfiguracionSunat;
use App\Models\Factura;
use Illuminate\Http\Request;

/**
 * Bandeja de comprobantes electrónicos: monitoreo de estados frente a SUNAT y
 * reprocesamiento de los que quedaron en error (individual o en lote).
 */
class BandejaSunatController extends Controller
{
    public array $estados = ['aceptado', 'observado', 'pendiente', 'error', 'rechazado', 'anulado'];

    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $estado = $request->string('estado')->trim();

        $comprobantes = ComprobanteElectronico::query()
            ->when($q->isNotEmpty(), fn ($w) => $w->where(fn ($s) => $s
                ->where('numero_completo', 'like', "%{$q}%")
                ->orWhere('receptor_nombre', 'like', "%{$q}%")
                ->orWhere('receptor_numero', 'like', "%{$q}%")))
            ->when($estado->isNotEmpty(), fn ($w) => $w->where('estado', (string) $estado))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $porEstado = ComprobanteElectronico::query()
            ->selectRaw('estado, count(*) as c')
            ->groupBy('estado')
            ->pluck('c', 'estado');

        $resumen = [
            'aceptado' => (int) ($porEstado['aceptado'] ?? 0) + (int) ($porEstado['observado'] ?? 0),
            'pendiente' => (int) ($porEstado['pendiente'] ?? 0) + (int) ($porEstado['enviado'] ?? 0),
            'error' => (int) ($porEstado['error'] ?? 0) + (int) ($porEstado['rechazado'] ?? 0),
            'total' => (int) $porEstado->sum(),
            // Comunicaciones de baja / resúmenes en proceso (con ticket SUNAT).
            'bajas' => (int) ComprobanteElectronico::where('estado', 'pendiente')->whereNotNull('ticket')->count(),
        ];

        return view('bandeja-sunat.index', [
            'comprobantes' => $comprobantes,
            'q' => $q,
            'estadoSel' => (string) $estado,
            'estados' => $this->estados,
            'resumen' => $resumen,
        ]);
    }

    /** Reprocesa (reintenta la emisión de) un comprobante. */
    public function reintentar(ComprobanteElectronico $comprobante, MapeadorFacturaErp $mapeador, EmitirComprobante $emitir)
    {
        $resultado = $this->emitirDeNuevo($comprobante, $mapeador, $emitir);

        return $resultado === null
            ? back()->with('error', 'El comprobante no tiene una factura asociada para reprocesar.')
            : back()->with(
                $resultado->exito ? 'success' : 'error',
                $resultado->exito
                    ? "Comprobante {$resultado->numeroCompleto} reprocesado — estado: {$resultado->estado->value}."
                    : "No se pudo reprocesar {$resultado->numeroCompleto}: {$resultado->mensaje}"
            );
    }

    /** Reprocesa en lote todos los comprobantes en error / pendientes. */
    public function reintentarLote(MapeadorFacturaErp $mapeador, EmitirComprobante $emitir)
    {
        $pendientes = ComprobanteElectronico::query()
            ->whereIn('estado', ['error', 'pendiente', 'rechazado'])
            ->whereNull('ticket') // los que tienen ticket son bajas en proceso, no re-emisiones
            ->get();

        $ok = 0;
        $fail = 0;
        foreach ($pendientes as $comprobante) {
            $r = $this->emitirDeNuevo($comprobante, $mapeador, $emitir);
            if ($r && $r->exito) {
                $ok++;
            } else {
                $fail++;
            }
        }

        if ($ok === 0 && $fail === 0) {
            return back()->with('success', 'No hay comprobantes en error para reprocesar.');
        }

        return back()->with('success', "Reproceso en lote: {$ok} aceptados, {$fail} con problemas.");
    }

    /**
     * Consulta en lote las Comunicaciones de Baja / Resúmenes que quedaron en
     * proceso (estado 'pendiente' con ticket). Confirma las aceptadas por SUNAT
     * (marca el comprobante y su factura como anulados) y reactiva las rechazadas.
     */
    public function consultarBajasPendientes(ResolverConfiguracionSunat $resolver, ConstructorUblGreenter $constructor)
    {
        $pendientes = ComprobanteElectronico::query()
            ->where('estado', 'pendiente')
            ->whereNotNull('ticket')
            ->get();

        if ($pendientes->isEmpty()) {
            return back()->with('success', 'No hay comunicaciones de baja en proceso para consultar.');
        }

        $credCache = []; // credenciales por clínica (se resuelven una sola vez)
        $anulados = 0;
        $rechazados = 0;
        $enProceso = 0;
        $sinCred = 0;

        foreach ($pendientes as $cpe) {
            $clinicaId = $cpe->clinica_id;

            if (! array_key_exists((int) $clinicaId, $credCache)) {
                try {
                    $credCache[(int) $clinicaId] = $resolver->paraClinica($clinicaId);
                } catch (\Throwable $e) {
                    $credCache[(int) $clinicaId] = null;
                }
            }
            $cred = $credCache[(int) $clinicaId];

            if ($cred === null) {
                $sinCred++;

                continue;
            }

            try {
                $r = $constructor->consultarBaja($cred, $cpe->ticket);
            } catch (\Throwable $e) {
                $enProceso++;

                continue;
            }

            if ($r['estado'] === 'anulado') {
                $cpe->update(['estado' => 'anulado', 'codigo_respuesta' => $r['codigo'] ?? '0', 'mensaje' => $r['mensaje'] ?? null]);
                if ($cpe->factura_id) {
                    Factura::where('id', $cpe->factura_id)->update(['estado' => 'anulada']);
                }
                $anulados++;
            } elseif ($r['estado'] === 'rechazado') {
                $cpe->update(['estado' => 'aceptado', 'mensaje' => $r['mensaje'] ?? null, 'ticket' => null]);
                $rechazados++;
            } else {
                $enProceso++;
            }
        }

        $partes = ["{$anulados} anuladas", "{$rechazados} rechazadas", "{$enProceso} aún en proceso"];
        if ($sinCred > 0) {
            $partes[] = "{$sinCred} sin credenciales reales";
        }

        return back()->with('success', 'Consulta de bajas: '.implode(', ', $partes).'.');
    }

    private function emitirDeNuevo(ComprobanteElectronico $comprobante, MapeadorFacturaErp $mapeador, EmitirComprobante $emitir)
    {
        if (! $comprobante->factura_id) {
            return null;
        }
        $factura = Factura::with(['items', 'paciente'])->find($comprobante->factura_id);
        if (! $factura) {
            return null;
        }

        $pais = Pais::from($comprobante->pais ?: 'PE');

        return $emitir->ejecutar($mapeador->desdeFactura($factura, $pais));
    }
}
