<?php

namespace App\Facturacion\Domain\Model;

/**
 * Línea de detalle del comprobante (ej. "Consulta médica - Cardiología").
 * Los importes se expresan en la moneda del comprobante.
 */
final readonly class Linea
{
    public function __construct(
        public string $descripcion,
        public float $cantidad,
        public float $precioUnitario,     // precio SIN impuesto
        public float $importe,            // cantidad * precioUnitario (base imponible)
        public float $igv = 0.0,          // impuesto de la línea
        public string $unidadMedida = 'NIU', // NIU = unidad (catálogo 03 SUNAT)
        public string $codigoProducto = '',
        public string $tipoAfectacionIgv = 'gravado', // gravado|exonerado|inafecto
    ) {
    }
}
