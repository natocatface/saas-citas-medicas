<?php

namespace App\Facturacion\Domain\Model;

use App\Facturacion\Domain\Enum\Moneda;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Enum\TipoComprobante;

/**
 * Comprobante electrónico en lenguaje de dominio, agnóstico al país.
 *
 * Es el "contrato de datos" que viaja desde el ERP hacia cualquier
 * ProveedorFacturacion. Ningún adaptador de país recibe modelos Eloquent:
 * sólo este objeto puro. Así el ERP no depende de la lógica de SUNAT/DIAN/etc.
 *
 * @param  Linea[]  $lineas
 * @param  string[] $documentosRelacionados  Series-correlativos afectados (para notas de crédito/débito)
 */
final readonly class Comprobante
{
    public function __construct(
        public Pais $pais,
        public TipoComprobante $tipo,
        public string $serie,             // ej. "F001"
        public string $correlativo,       // ej. "00000123"
        public \DateTimeImmutable $fechaEmision,
        public Moneda $moneda,
        public Emisor $emisor,
        public Receptor $receptor,
        public array $lineas,
        public Totales $totales,
        public array $documentosRelacionados = [],
        public ?string $motivoNota = null,       // sólo NC/ND
        public ?string $codigoMotivoNota = null, // catálogo local del motivo
        public array $metadata = [],             // datos libres del ERP (factura_id, cita_id, clinica_id...)
    ) {
    }

    public function numeroCompleto(): string
    {
        return "{$this->serie}-{$this->correlativo}";
    }
}
