<?php

namespace App\Facturacion\Domain\Result;

use App\Facturacion\Domain\Enum\EstadoComprobante;

final readonly class ResultadoConsulta
{
    public function __construct(
        public EstadoComprobante $estado,
        public ?string $codigoRespuesta = null,
        public ?string $mensaje = null,
        public array $observaciones = [],
    ) {
    }
}
