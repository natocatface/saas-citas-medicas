<?php

namespace App\Http\Controllers;

use App\Facturacion\Application\DTO\MapeadorFacturaErp;
use App\Facturacion\Application\Services\AnularComprobante;
use App\Facturacion\Application\Services\ConsultarEstado;
use App\Facturacion\Application\Services\EmitirComprobante;
use App\Facturacion\Application\Services\EmitirNotaCredito;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico;
use App\Facturacion\Infrastructure\Persistence\Models\FacturacionConfiguracion;
use App\Facturacion\Infrastructure\Providers\Peru\ConstructorUblGreenter;
use App\Facturacion\Infrastructure\Providers\Peru\ResolverConfiguracionSunat;
use App\Models\Cita;
use App\Models\Factura;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FacturaController extends Controller
{
    public array $estados = ['pendiente', 'pagada', 'anulada'];
    public array $metodos = ['Efectivo', 'Tarjeta', 'Transferencia', 'Seguro'];

    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $estado = $request->string('estado')->trim();

        $facturas = Factura::query()
            ->with(['paciente', 'comprobanteElectronico'])
            ->when($q->isNotEmpty(), function ($w) use ($q) {
                $w->where('numero', 'like', "%{$q}%")
                  ->orWhereHas('paciente', fn ($s) => $s->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%"));
            })
            ->when($estado->isNotEmpty(), fn ($w) => $w->where('estado', (string) $estado))
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(12)->withQueryString();

        $totales = [
            'pagado' => (float) Factura::where('estado', 'pagada')->sum('total'),
            'pendiente' => (float) Factura::where('estado', 'pendiente')->sum('total'),
            'count' => Factura::count(),
        ];

        return view('facturacion.index', [
            'facturas' => $facturas, 'q' => $q, 'estadoSel' => (string) $estado,
            'estados' => $this->estados, 'totales' => $totales,
        ]);
    }

    public function create()
    {
        return view('facturacion.create', [
            'factura' => new Factura(['fecha' => now()->toDateString(), 'estado' => 'pendiente']),
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'estados' => $this->estados, 'metodos' => $this->metodos, 'items' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $factura = DB::transaction(function () use ($data, $request) {
            $numero = 'FAC-'.str_pad((string) ((Factura::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT);
            $factura = Factura::create([
                'numero' => $numero,
                'paciente_id' => $data['paciente_id'],
                'cita_id' => $data['cita_id'] ?? null,
                'fecha' => $data['fecha'],
                'estado' => $data['estado'],
                'metodo_pago' => $data['metodo_pago'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'impuesto' => $data['impuesto'] ?? 0,
                'notas' => $data['notas'] ?? null,
            ]);
            $this->guardarItems($factura, $request->input('items', []));

            return $factura;
        });

        $emitida = $this->emitirAutomaticoSiCorresponde($factura);
        $msg = 'Factura creada correctamente.'.($emitida ? ' Comprobante electrónico emitido.' : '');

        return redirect()->route('facturacion.index')->with('success', $msg);
    }

    /** Emite el comprobante al crear la factura si la clínica lo tiene configurado. */
    private function emitirAutomaticoSiCorresponde(Factura $factura): bool
    {
        $cfg = FacturacionConfiguracion::where('clinica_id', $factura->clinica_id)->first();
        if (! $cfg || ! $cfg->emitir_automatico) {
            return false;
        }

        try {
            $pais = Pais::from((string) config('facturacion.pais_defecto', 'PE'));
            $comprobante = app(MapeadorFacturaErp::class)->desdeFactura($factura->load(['items', 'paciente']), $pais);
            app(EmitirComprobante::class)->ejecutar($comprobante);

            return true;
        } catch (\Throwable $e) {
            report($e); // no romper la creación de la factura si SUNAT falla

            return false;
        }
    }

    public function show(Factura $facturacion)
    {
        $facturacion->load(['paciente.aseguradora', 'items', 'cita', 'comprobanteElectronico']);
        return view('facturacion.show', ['factura' => $facturacion]);
    }

    public function edit(Factura $facturacion)
    {
        $facturacion->load('items');
        return view('facturacion.edit', [
            'factura' => $facturacion,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'estados' => $this->estados, 'metodos' => $this->metodos,
            'items' => $facturacion->items,
        ]);
    }

    public function update(Request $request, Factura $facturacion)
    {
        $data = $this->validar($request);
        DB::transaction(function () use ($data, $request, $facturacion) {
            $facturacion->update([
                'paciente_id' => $data['paciente_id'],
                'cita_id' => $data['cita_id'] ?? null,
                'fecha' => $data['fecha'],
                'estado' => $data['estado'],
                'metodo_pago' => $data['metodo_pago'] ?? null,
                'descuento' => $data['descuento'] ?? 0,
                'impuesto' => $data['impuesto'] ?? 0,
                'notas' => $data['notas'] ?? null,
            ]);
            $facturacion->items()->delete();
            $this->guardarItems($facturacion, $request->input('items', []));
        });
        return redirect()->route('facturacion.index')->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(Factura $facturacion)
    {
        $facturacion->delete();
        return redirect()->route('facturacion.index')->with('success', 'Factura eliminada.');
    }

    public function pagar(Request $request, Factura $facturacion)
    {
        $facturacion->update([
            'estado' => 'pagada',
            'metodo_pago' => $request->input('metodo_pago', $facturacion->metodo_pago ?: 'Efectivo'),
        ]);
        return back()->with('success', 'Factura marcada como pagada.');
    }

    /**
     * Emite el comprobante electrónico de esta factura ante el organismo del país
     * configurado (Perú/SUNAT por defecto). Delega en el contexto acotado
     * App\Facturacion; el ERP sólo dispara el caso de uso y muestra el resultado.
     * Síncrono para dar feedback inmediato (en modo simulado es instantáneo).
     */
    public function emitirCpe(Factura $facturacion, MapeadorFacturaErp $mapeador, EmitirComprobante $emitir)
    {
        if ($facturacion->estado === 'anulada') {
            return back()->with('error', 'No se puede emitir un comprobante de una factura anulada.');
        }

        $pais = Pais::from((string) config('facturacion.pais_defecto', 'PE'));
        $comprobante = $mapeador->desdeFactura($facturacion->load(['items', 'paciente']), $pais);
        $resultado = $emitir->ejecutar($comprobante);

        return $resultado->exito
            ? back()->with('success', "Comprobante {$resultado->numeroCompleto} emitido — estado: {$resultado->estado->value}.")
            : back()->with('error', "No se pudo emitir: {$resultado->mensaje}");
    }

    /** Reconsulta el estado del comprobante ante el organismo. */
    public function consultarCpe(Factura $facturacion, MapeadorFacturaErp $mapeador, ConsultarEstado $consultar)
    {
        $pais = Pais::from((string) config('facturacion.pais_defecto', 'PE'));
        $r = $consultar->ejecutar($mapeador->desdeFactura($facturacion->load(['items', 'paciente']), $pais));

        return back()->with('success', "Estado ante el organismo: {$r->estado->value}".($r->mensaje ? " — {$r->mensaje}" : ''));
    }

    /**
     * Emite una NOTA DE CRÉDITO que afecta al comprobante ya emitido de esta
     * factura (p. ej. anulación o devolución). Requiere que la factura/boleta
     * ya esté aceptada por el organismo.
     */
    public function emitirNotaCredito(Request $request, Factura $facturacion, MapeadorFacturaErp $mapeador, EmitirNotaCredito $emitir)
    {
        $data = $request->validate([
            'motivo' => ['required', 'string', 'max:250'],
            'codigo_motivo' => ['nullable', 'string', 'max:2'],
        ]);

        $tieneAceptado = ComprobanteElectronico::withoutGlobalScopes()
            ->where('factura_id', $facturacion->id)
            ->whereIn('tipo', ['factura', 'boleta'])
            ->whereIn('estado', ['aceptado', 'observado'])
            ->exists();

        if (! $tieneAceptado) {
            return back()->with('error', 'Primero emite y acepta el comprobante (factura/boleta) antes de generar una nota de crédito.');
        }

        $pais = Pais::from((string) config('facturacion.pais_defecto', 'PE'));
        $nc = $mapeador->notaCreditoDesdeFactura($facturacion->load(['items', 'paciente']), $data['motivo'], $data['codigo_motivo'] ?? '01', $pais);
        $resultado = $emitir->ejecutar($nc);

        return $resultado->exito
            ? back()->with('success', "Nota de crédito {$resultado->numeroCompleto} emitida — estado: {$resultado->estado->value}.")
            : back()->with('error', "No se pudo emitir la nota de crédito: {$resultado->mensaje}");
    }

    /**
     * Anula (da de baja) el comprobante electrónico de la factura. En modo real,
     * SUNAT recomienda la Nota de Crédito para comprobantes ya entregados; en
     * modo simulado marca el comprobante como anulado.
     */
    public function anularCpe(Request $request, Factura $facturacion, MapeadorFacturaErp $mapeador, AnularComprobante $anular)
    {
        $data = $request->validate(['motivo' => ['required', 'string', 'max:250']]);

        $existe = ComprobanteElectronico::withoutGlobalScopes()
            ->where('factura_id', $facturacion->id)
            ->whereIn('tipo', ['factura', 'boleta'])
            ->exists();

        if (! $existe) {
            return back()->with('error', 'Esta factura no tiene comprobante electrónico para anular.');
        }

        $pais = Pais::from((string) config('facturacion.pais_defecto', 'PE'));
        $comprobante = $mapeador->desdeFactura($facturacion->load(['items', 'paciente']), $pais);
        $resultado = $anular->ejecutar($comprobante, $data['motivo']);

        // Aceptada de inmediato (modo simulado o baja confirmada en el sondeo).
        if ($resultado->estado === EstadoComprobante::ANULADO) {
            $facturacion->update(['estado' => 'anulada']);

            return back()->with('success', "Comprobante {$resultado->numeroCompleto} anulado — estado: anulado.");
        }

        // Comunicación de Baja enviada; SUNAT la procesa de forma asíncrona.
        if ($resultado->estado === EstadoComprobante::PENDIENTE) {
            return back()->with('success', 'Comunicación de baja enviada a SUNAT (ticket '.$resultado->ticket.'). Usa "Consultar baja" en unos minutos para confirmar la anulación.');
        }

        return back()->with('error', $resultado->mensaje);
    }

    /**
     * Consulta el estado de una Comunicación de Baja en proceso (por ticket) y
     * actualiza el comprobante: anulado si SUNAT la aceptó, o vuelve a vigente si
     * la rechazó.
     */
    public function consultarBajaCpe(Factura $facturacion, ResolverConfiguracionSunat $resolver, ConstructorUblGreenter $constructor)
    {
        $cpe = ComprobanteElectronico::withoutGlobalScopes()
            ->where('factura_id', $facturacion->id)
            ->whereIn('tipo', ['factura', 'boleta'])
            ->orderByDesc('id')
            ->first();

        if (! $cpe || ! $cpe->ticket) {
            return back()->with('error', 'No hay una comunicación de baja en proceso para consultar.');
        }

        try {
            $cred = $resolver->paraClinica($facturacion->clinica_id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Configuración/certificado: '.$e->getMessage());
        }

        if ($cred === null) {
            return back()->with('error', 'La facturación real no está habilitada para consultar la baja.');
        }

        $r = $constructor->consultarBaja($cred, $cpe->ticket);

        if ($r['estado'] === 'anulado') {
            $cpe->update(['estado' => 'anulado', 'codigo_respuesta' => $r['codigo'] ?? '0', 'mensaje' => $r['mensaje'] ?? null]);
            $facturacion->update(['estado' => 'anulada']);

            return back()->with('success', 'Baja aceptada por SUNAT: '.($r['mensaje'] ?? 'comprobante anulado').'.');
        }

        if ($r['estado'] === 'rechazado') {
            $cpe->update(['estado' => 'aceptado', 'mensaje' => $r['mensaje'] ?? null, 'ticket' => null]);

            return back()->with('error', 'SUNAT rechazó la baja: '.($r['mensaje'] ?? '').'. El comprobante sigue vigente.');
        }

        return back()->with('success', 'La baja sigue en proceso en SUNAT. Vuelve a consultar en unos minutos.');
    }

    /** Descarga el XML firmado almacenado del comprobante electrónico. */
    public function descargarXml(Factura $facturacion)
    {
        $cpe = $facturacion->comprobanteElectronico;
        abort_unless($cpe && $cpe->ruta_xml, 404, 'Sin XML disponible.');

        $disk = Storage::disk((string) config('facturacion.disco', 'local'));
        abort_unless($disk->exists($cpe->ruta_xml), 404, 'El XML no se encuentra en el almacén.');

        return response($disk->get($cpe->ruta_xml), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="'.$cpe->numero_completo.'.xml"',
        ]);
    }

    /**
     * Representación impresa (formato SUNAT) del comprobante electrónico.
     * Vista A4 autocontenida; el usuario la imprime o guarda como PDF.
     */
    public function imprimirCpe(Factura $facturacion)
    {
        $facturacion->load(['items', 'paciente', 'comprobanteElectronico']);
        $cpe = $facturacion->comprobanteElectronico;
        abort_unless($cpe, 404, 'Esta factura aún no tiene comprobante electrónico emitido.');

        $cfg = FacturacionConfiguracion::where('clinica_id', $facturacion->clinica_id)->first();
        $emisor = [
            'ruc' => $cfg->ruc ?? config('facturacion.emisor.ruc'),
            'razon_social' => $cfg->razon_social ?? config('facturacion.emisor.razon_social'),
            'nombre_comercial' => $cfg->nombre_comercial ?? null,
            'direccion' => $cfg->direccion ?? null,
        ];

        $qrData = $this->cadenaQr($cpe, (string) $emisor['ruc']);

        return view('facturacion.cpe-print', [
            'factura' => $facturacion,
            'cpe' => $cpe,
            'emisor' => $emisor,
            'enLetras' => $this->montoEnLetras((float) $cpe->total),
            'qrData' => $qrData,
            'qrImg' => $this->imagenQr($qrData),
        ]);
    }

    /** Cadena del código QR según especificación SUNAT. */
    private function cadenaQr(\App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico $cpe, string $ruc): string
    {
        $tipoMap = ['factura' => '01', 'boleta' => '03', 'nota_credito' => '07', 'nota_debito' => '08'];
        $docMap = ['ruc' => '6', 'dni' => '1', 'ce' => '4', 'sin' => '0'];

        return implode('|', [
            $ruc,
            $tipoMap[$cpe->tipo] ?? '01',
            $cpe->serie,
            $cpe->correlativo,
            number_format((float) $cpe->igv, 2, '.', ''),
            number_format((float) $cpe->total, 2, '.', ''),
            optional($cpe->fecha_emision)->format('Y-m-d'),
            $docMap[$cpe->receptor_doc] ?? '0',
            $cpe->receptor_numero,
            $cpe->hash_cpe ?? '',
        ]);
    }

    /** Genera el QR como data-URI (PNG) si endroid/qr-code está instalado. */
    private function imagenQr(string $data): ?string
    {
        if (! class_exists(\Endroid\QrCode\Builder\Builder::class)) {
            return null;
        }
        try {
            return \Endroid\QrCode\Builder\Builder::create()
                ->data($data)->size(150)->margin(6)->build()->getDataUri();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** "SON: CIENTO CincUENTA CON 00/100 SOLES" a partir del monto. */
    private function montoEnLetras(float $monto): string
    {
        $entero = (int) floor($monto + 0.00001);
        $cent = (int) round(($monto - $entero) * 100);

        return sprintf('SON: %s CON %02d/100 SOLES', $this->numeroALetras($entero), $cent);
    }

    private function numeroALetras(int $n): string
    {
        if ($n === 0) {
            return 'CERO';
        }
        if ($n >= 1000000) {
            return number_format($n); // fallback para montos muy altos
        }

        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $esp = [10 => 'DIEZ', 11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE', 15 => 'QUINCE',
            16 => 'DIECISEIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE'];
        $veinti = ['', 'VEINTIUNO', 'VEINTIDOS', 'VEINTITRES', 'VEINTICUATRO', 'VEINTICINCO', 'VEINTISEIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE'];
        $decenas = [3 => 'TREINTA', 4 => 'CUARENTA', 5 => 'CINCUENTA', 6 => 'SESENTA', 7 => 'SETENTA', 8 => 'OCHENTA', 9 => 'NOVENTA'];
        $centenas = [1 => 'CIENTO', 2 => 'DOSCIENTOS', 3 => 'TRESCIENTOS', 4 => 'CUATROCIENTOS', 5 => 'QUINIENTOS',
            6 => 'SEISCIENTOS', 7 => 'SETECIENTOS', 8 => 'OCHOCIENTOS', 9 => 'NOVECIENTOS'];

        $menor1000 = function (int $x) use ($unidades, $esp, $veinti, $decenas, $centenas): string {
            $out = '';
            $c = intdiv($x, 100);
            $r = $x % 100;
            if ($c > 0) {
                $out .= ($x === 100) ? 'CIEN' : $centenas[$c];
            }
            if ($r > 0) {
                if ($out !== '') {
                    $out .= ' ';
                }
                if ($r < 10) {
                    $out .= $unidades[$r];
                } elseif ($r < 20) {
                    $out .= $esp[$r];
                } elseif ($r < 30) {
                    $out .= $veinti[$r - 20];
                } else {
                    $d = intdiv($r, 10);
                    $u = $r % 10;
                    $out .= $decenas[$d].($u > 0 ? ' Y '.$unidades[$u] : '');
                }
            }

            return trim($out);
        };

        $miles = intdiv($n, 1000);
        $resto = $n % 1000;
        $txt = '';
        if ($miles > 0) {
            $txt = ($miles === 1 ? 'MIL' : $menor1000($miles).' MIL');
        }
        if ($resto > 0) {
            $txt = trim($txt.' '.$menor1000($resto));
        }

        return $txt;
    }

    private function guardarItems(Factura $factura, array $items): void
    {
        $subtotal = 0;
        foreach ($items as $row) {
            $desc = trim((string) ($row['descripcion'] ?? ''));
            if ($desc === '') {
                continue;
            }
            $cant = (float) ($row['cantidad'] ?? 1);
            $precio = (float) ($row['precio_unitario'] ?? 0);
            $importe = round($cant * $precio, 2);
            $subtotal += $importe;
            $factura->items()->create([
                'descripcion' => $desc, 'cantidad' => $cant,
                'precio_unitario' => $precio, 'importe' => $importe,
            ]);
        }
        $total = max(0, $subtotal - (float) $factura->descuento + (float) $factura->impuesto);
        $factura->update(['subtotal' => $subtotal, 'total' => $total]);
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'cita_id' => ['nullable', 'exists:citas,id'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', Rule::in($this->estados)],
            'metodo_pago' => ['nullable', 'string', 'max:50'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'impuesto' => ['nullable', 'numeric', 'min:0'],
            'notas' => ['nullable', 'string', 'max:1000'],
            'items' => ['array'],
        ]);
    }
}
