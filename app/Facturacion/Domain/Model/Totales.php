<?php

namespace App\Facturacion\Domain\Model;

final readonly class Totales
{
    public function __construct(
        public float $gravado,      // base imponible de operaciones gravadas
        public float $exonerado,
        public float $inafecto,
        public float $igv,          // total de impuesto (IGV/IVA)
        public float $descuento,
        public float $total,        // importe total a pagar
    ) {
    }
}
