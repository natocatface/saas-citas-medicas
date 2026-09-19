<?php

namespace App\Facturacion\Interface\Http;

use App\Facturacion\Application\DTO\MapeadorFacturaErp;
use App\Facturacion\Application\Services\AnularComprobante;
use App\Facturacion\Application\Services\ConsultarEstado;
use App\Facturacion\Application\Services\EmitirComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Infrastructure\Jobs\EmitirComprobanteJob;
use App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico;
use App\Models\Factura;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * ADAPTADOR DE ENTRADA (REST). Traduce HTTP -> casos de uso.
 * El ERP puede llamar estos endpoints (hoy in-process, mañana por red).
 */
class FacturacionApiController extends Controller
{
    public function __construct(private readonly MapeadorFacturaErp $mapeador)
    {
    }

    /** Emite un comprobante a partir de una Factura del ERP. Encola por defecto. */
    public function emitir(Request $request, EmitirComprobante $emitir): JsonResponse
    {
        $data = $request->validate([
            'factura_id' => ['required', 'integer', 'exists:facturas,id'],
            'pais' => ['nullable', 'string', 'size:2'],
            'sincrono' => ['nullable', 'boolean'],
        ]);

        $factura = Factura::with(['items', 'paciente'])->findOrFail($data['factura_id']);
        $pais = Pais::from($data['pais'] ?? config('facturacion.pais_defecto', 'PE'));
        $comprobante = $this->mapeador->desdeFactura($factura, $pais);

        // Asíncrono (recomendado): responde 202 y procesa en segundo plano.
        if (! ($data['sincrono'] ?? false)) {
            EmitirComprobanteJob::dispatch($comprobante);

            return response()->json([
                'estado' => 'encolado',
                'numero' => $comprobante->numeroCompleto(),
            ], 202);
        }

        $resultado = $emitir->ejecutar($comprobante);

        return response()->json($this->serializar($resultado), $resultado->exito ? 201 : 422);
    }

    public function anular(string $numero, Request $request, AnularComprobante $anular, MapeadorFacturaErp $mapeador): JsonResponse
    {
        $data = $request->validate(['motivo' => ['required', 'string', 'max:250']]);
        $registro = ComprobanteElectronico::where('numero_completo', $numero)->firstOrFail();
        $comprobante = $mapeador->desdeFactura(Factura::findOrFail($registro->factura_id), Pais::from($registro->pais));

        $resultado = $anular->ejecutar($comprobante, $data['motivo']);

        return response()->json($this->serializar($resultado), $resultado->exito ? 200 : 422);
    }

    public function notaCredito(Request $request, EmitirComprobante $emitir): JsonResponse
    {
        // El mapeo de nota de crédito reutiliza el mismo flujo; se omite por brevedad
        // del scaffolding (ver EmitirNotaCredito en la capa de aplicación).
        return response()->json(['mensaje' => 'Endpoint de nota de crédito — ver EmitirNotaCredito.'], 501);
    }

    public function estado(string $numero, ConsultarEstado $consultar, MapeadorFacturaErp $mapeador): JsonResponse
    {
        $registro = ComprobanteElectronico::where('numero_completo', $numero)->firstOrFail();
        $comprobante = $mapeador->desdeFactura(Factura::findOrFail($registro->factura_id), Pais::from($registro->pais));

        $r = $consultar->ejecutar($comprobante);

        return response()->json([
            'numero' => $numero,
            'estado' => $r->estado->value,
            'codigo' => $r->codigoRespuesta,
            'mensaje' => $r->mensaje,
            'observaciones' => $r->observaciones,
        ]);
    }

    private function serializar(\App\Facturacion\Domain\Result\ResultadoEmision $r): array
    {
        return [
            'exito' => $r->exito,
            'numero' => $r->numeroCompleto,
            'estado' => $r->estado->value,
            'codigo' => $r->codigoRespuesta,
            'mensaje' => $r->mensaje,
            'hash' => $r->hashCpe,
            'xml' => $r->rutaXml,
            'cdr' => $r->rutaCdr,
            'pdf' => $r->rutaPdf,
            'observaciones' => $r->observaciones,
        ];
    }
}
