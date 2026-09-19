<?php

namespace App\Facturacion\Application\DTO;

use App\Facturacion\Domain\Enum\Moneda;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Enum\TipoComprobante;
use App\Facturacion\Domain\Enum\TipoDocumentoIdentidad;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Model\Emisor;
use App\Facturacion\Domain\Model\Linea;
use App\Facturacion\Domain\Model\Receptor;
use App\Facturacion\Domain\Model\Totales;
use App\Facturacion\Domain\Contracts\RepositorioComprobantes;
use App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico;
use App\Facturacion\Infrastructure\Persistence\Models\FacturacionConfiguracion;
use App\Models\Factura;

/**
 * ANTICORRUPTION LAYER.
 *
 * Traduce la Factura interna del ERP (Eloquent) al Comprobante de dominio.
 * Es el ÚNICO punto que conoce ambos mundos; así el módulo de facturación
 * electrónica no se contamina con detalles del ERP y viceversa.
 */
final class MapeadorFacturaErp
{
    public function __construct(private readonly RepositorioComprobantes $repositorio)
    {
    }

    public function desdeFactura(Factura $factura, Pais $pais = Pais::PE): Comprobante
    {
        $factura->loadMissing(['items', 'paciente']);

        $cfg = $this->configDeClinica($factura->clinica_id);
        $tasa = $cfg && $cfg->igv_tasa !== null ? ((float) $cfg->igv_tasa) / 100 : 0.18;

        $emisor = $this->emisor($cfg);
        $receptor = $this->receptorDesdePaciente($factura);
        $tipo = $this->tipoComprobante($receptor);

        $lineas = $factura->items->map(fn ($item) => new Linea(
            descripcion: (string) $item->descripcion,
            cantidad: (float) $item->cantidad,
            precioUnitario: (float) $item->precio_unitario,
            importe: (float) $item->importe,
            igv: round((float) $item->importe * $tasa, 2),
        ))->all();

        [$serie, $correlativo] = $this->serieCorrelativo($factura, $cfg, $tipo);

        return new Comprobante(
            pais: $pais,
            tipo: $tipo,
            serie: $serie,
            correlativo: $correlativo,
            fechaEmision: new \DateTimeImmutable($factura->fecha?->toDateString() ?? 'today'),
            moneda: Moneda::PEN,
            emisor: $emisor,
            receptor: $receptor,
            lineas: $lineas,
            totales: new Totales(
                gravado: (float) $factura->subtotal,
                exonerado: 0.0,
                inafecto: 0.0,
                igv: (float) $factura->impuesto,
                descuento: (float) $factura->descuento,
                total: (float) $factura->total,
            ),
            metadata: [
                'factura_id' => $factura->id,
                'cita_id' => $factura->cita_id,
                'clinica_id' => $factura->clinica_id,
                'numero_erp' => $factura->numero,
            ],
        );
    }

    /**
     * Construye una NOTA DE CRÉDITO que afecta al comprobante ya emitido de esta
     * factura. El motivo sigue el catálogo 09 de SUNAT (01 = anulación de la
     * operación, 07 = devolución por ítem, etc.).
     */
    public function notaCreditoDesdeFactura(
        Factura $factura,
        string $motivo,
        string $codigoMotivo = '01',
        Pais $pais = Pais::PE
    ): Comprobante {
        $factura->loadMissing(['items', 'paciente']);

        $cfg = $this->configDeClinica($factura->clinica_id);
        $tasa = $cfg && $cfg->igv_tasa !== null ? ((float) $cfg->igv_tasa) / 100 : 0.18;

        $lineas = $factura->items->map(fn ($item) => new Linea(
            descripcion: (string) $item->descripcion,
            cantidad: (float) $item->cantidad,
            precioUnitario: (float) $item->precio_unitario,
            importe: (float) $item->importe,
            igv: round((float) $item->importe * $tasa, 2),
        ))->all();

        $serie = $cfg->serie_nota_credito ?? config('facturacion.series.nota_credito', 'FC01');
        $correlativo = $this->correlativoPara($factura, $serie, TipoComprobante::NOTA_CREDITO);
        $afectado = $this->documentoAfectado($factura);

        return new Comprobante(
            pais: $pais,
            tipo: TipoComprobante::NOTA_CREDITO,
            serie: $serie,
            correlativo: $correlativo,
            fechaEmision: new \DateTimeImmutable($factura->fecha?->toDateString() ?? 'today'),
            moneda: Moneda::PEN,
            emisor: $this->emisor($cfg),
            receptor: $this->receptorDesdePaciente($factura),
            lineas: $lineas,
            totales: new Totales(
                gravado: (float) $factura->subtotal,
                exonerado: 0.0,
                inafecto: 0.0,
                igv: (float) $factura->impuesto,
                descuento: (float) $factura->descuento,
                total: (float) $factura->total,
            ),
            documentosRelacionados: $afectado ? [$afectado] : [],
            motivoNota: $motivo,
            codigoMotivoNota: $codigoMotivo,
            metadata: [
                'factura_id' => $factura->id,
                'cita_id' => $factura->cita_id,
                'clinica_id' => $factura->clinica_id,
                'numero_erp' => $factura->numero,
                'afecta' => $afectado,
            ],
        );
    }

    private function configDeClinica(?int $clinicaId): ?FacturacionConfiguracion
    {
        return FacturacionConfiguracion::query()
            ->when($clinicaId, fn ($q) => $q->where('clinica_id', $clinicaId))
            ->first();
    }

    private function emisor(?FacturacionConfiguracion $cfg): Emisor
    {
        return new Emisor(
            ruc: (string) ($cfg->ruc ?? config('facturacion.emisor.ruc', '20000000001')),
            razonSocial: (string) ($cfg->razon_social ?? config('facturacion.emisor.razon_social', 'CLINICA DEMO S.A.C.')),
            nombreComercial: $cfg->nombre_comercial ?? null,
            direccion: $cfg->direccion ?? null,
            ubigeo: $cfg->ubigeo ?? null,
        );
    }

    private function receptorDesdePaciente(Factura $factura): Receptor
    {
        $p = $factura->paciente;
        $doc = $p->documento ?? '';
        $tipo = strlen($doc) === 11 ? TipoDocumentoIdentidad::RUC
            : ($doc !== '' ? TipoDocumentoIdentidad::DNI : TipoDocumentoIdentidad::SIN);

        return new Receptor(
            tipoDocumento: $tipo,
            numeroDocumento: $doc !== '' ? $doc : '00000000',
            razonSocial: trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) ?: 'CONSUMIDOR FINAL',
            email: $p->email ?? null,
        );
    }

    private function tipoComprobante(Receptor $receptor): TipoComprobante
    {
        // Con RUC -> Factura; en otro caso -> Boleta (regla de negocio típica en Perú).
        return $receptor->tipoDocumento === TipoDocumentoIdentidad::RUC
            ? TipoComprobante::FACTURA
            : TipoComprobante::BOLETA;
    }

    /** @return array{0:string,1:string} [serie, correlativo] */
    private function serieCorrelativo(Factura $factura, ?FacturacionConfiguracion $cfg, TipoComprobante $tipo): array
    {
        // La serie sale de la configuración de la clínica según el tipo.
        $serie = match ($tipo) {
            TipoComprobante::BOLETA => $cfg->serie_boleta ?? config('facturacion.series.boleta', 'B001'),
            TipoComprobante::NOTA_CREDITO => $cfg->serie_nota_credito ?? config('facturacion.series.nota_credito', 'FC01'),
            default => $cfg->serie_factura ?? config('facturacion.series.factura', 'F001'),
        };

        return [$serie, $this->correlativoPara($factura, $serie, $tipo)];
    }

    /**
     * Correlativo del comprobante, garantizando la numeración que exige SUNAT:
     *  - Si la factura YA generó un comprobante de este tipo y serie, se reutiliza
     *    su correlativo (re-emitir o consultar NO cambia el número).
     *  - Si es nuevo, se pide el siguiente correlativo ATÓMICO de la serie al
     *    repositorio (secuencial, continuo y sin duplicados aun con concurrencia).
     */
    private function correlativoPara(Factura $factura, string $serie, TipoComprobante $tipo): string
    {
        $existente = ComprobanteElectronico::withoutGlobalScopes()
            ->where('factura_id', $factura->id)
            ->where('tipo', $tipo->value)
            ->where('serie', $serie)
            ->value('correlativo');

        if ($existente) {
            return $existente;
        }

        return $this->repositorio->siguienteCorrelativo((int) ($factura->clinica_id ?? 0), $serie);
    }

    /** Serie-correlativo del comprobante (factura/boleta) que una NC afecta. */
    private function documentoAfectado(Factura $factura): ?string
    {
        return ComprobanteElectronico::withoutGlobalScopes()
            ->where('factura_id', $factura->id)
            ->whereIn('tipo', ['factura', 'boleta'])
            ->orderByDesc('id')
            ->value('numero_completo');
    }
}
