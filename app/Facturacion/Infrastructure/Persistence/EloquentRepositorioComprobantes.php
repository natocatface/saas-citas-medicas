<?php

namespace App\Facturacion\Infrastructure\Persistence;

use App\Facturacion\Domain\Contracts\RepositorioComprobantes;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoEmision;
use App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico;
use App\Facturacion\Infrastructure\Persistence\Models\ComprobanteEvento;
use Illuminate\Support\Facades\DB;

/**
 * Adaptador de persistencia con Eloquent. Implementa el puerto del dominio.
 */
final class EloquentRepositorioComprobantes implements RepositorioComprobantes
{
    public function guardar(Comprobante $c, ResultadoEmision $r): int
    {
        $registro = ComprobanteElectronico::withoutGlobalScopes()->firstOrNew([
            'numero_completo' => $c->numeroCompleto(),
            'emisor_ruc' => $c->emisor->ruc,
        ]);

        $registro->fill([
            'clinica_id' => $c->metadata['clinica_id'] ?? $registro->clinica_id,
            'factura_id' => $c->metadata['factura_id'] ?? $registro->factura_id,
            'pais' => $c->pais->value,
            'tipo' => $c->tipo->value,
            'serie' => $c->serie,
            'correlativo' => $c->correlativo,
            'moneda' => $c->moneda->value,
            'receptor_doc' => $c->receptor->tipoDocumento->value,
            'receptor_numero' => $c->receptor->numeroDocumento,
            'receptor_nombre' => $c->receptor->razonSocial,
            'fecha_emision' => $c->fechaEmision->format('Y-m-d'),
            'gravado' => $c->totales->gravado,
            'igv' => $c->totales->igv,
            'total' => $c->totales->total,
            'estado' => $r->estado->value,
            'codigo_respuesta' => $r->codigoRespuesta,
            'mensaje' => $r->mensaje,
            'observaciones' => $r->observaciones,
        ]);

        // El hash, ticket y las rutas de artefactos sólo se sobreescriben si el
        // resultado trae uno nuevo. Así una Comunicación de Baja no borra el XML
        // ni el CDR de la emisión original del comprobante.
        if ($r->hashCpe !== null) {
            $registro->hash_cpe = $r->hashCpe;
        }
        if ($r->ticket !== null) {
            $registro->ticket = $r->ticket;
        }
        if ($r->rutaXml !== null) {
            $registro->ruta_xml = $r->rutaXml;
        }
        if ($r->rutaCdr !== null) {
            $registro->ruta_cdr = $r->rutaCdr;
        }
        if ($r->rutaPdf !== null) {
            $registro->ruta_pdf = $r->rutaPdf;
        }

        $registro->save();

        return $registro->id;
    }

    public function registrarEvento(string $numeroCompleto, string $evento, array $payload = []): void
    {
        $id = ComprobanteElectronico::where('numero_completo', $numeroCompleto)->value('id');

        ComprobanteEvento::create([
            'comprobante_id' => $id,
            'numero_completo' => $numeroCompleto,
            'evento' => $evento,
            'payload' => $payload,
        ]);
    }

    public function siguienteCorrelativo(int $clinicaId, string $serie): string
    {
        // Correlativo atómico por (clinica, serie) usando un lock de fila.
        // clinicaId = 0 significa instalación de un solo emisor (clinica_id NULL).
        return DB::transaction(function () use ($clinicaId, $serie) {
            $max = ComprobanteElectronico::withoutGlobalScopes()
                ->where('serie', $serie)
                ->when(
                    $clinicaId > 0,
                    fn ($q) => $q->where('clinica_id', $clinicaId),
                    fn ($q) => $q->whereNull('clinica_id'),
                )
                ->lockForUpdate()
                ->max('correlativo');

            return str_pad((string) (((int) $max) + 1), 8, '0', STR_PAD_LEFT);
        });
    }
}
