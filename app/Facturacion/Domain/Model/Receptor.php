<?php

namespace App\Facturacion\Domain\Model;

use App\Facturacion\Domain\Enum\TipoDocumentoIdentidad;

/**
 * Datos del receptor (el paciente o la aseguradora que recibe el comprobante).
 */
final readonly class Receptor
{
    public function __construct(
        public TipoDocumentoIdentidad $tipoDocumento,
        public string $numeroDocumento,
        public string $razonSocial,       // nombre del paciente o razón social
        public ?string $direccion = null,
        public ?string $email = null,
    ) {
    }
}
