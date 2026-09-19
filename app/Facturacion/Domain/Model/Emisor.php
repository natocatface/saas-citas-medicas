<?php

namespace App\Facturacion\Domain\Model;

/**
 * Datos del emisor (la clínica/empresa que factura).
 * Se hidrata desde la configuración de la clínica (multi-tenant).
 */
final readonly class Emisor
{
    public function __construct(
        public string $ruc,               // identificador fiscal del emisor
        public string $razonSocial,
        public ?string $nombreComercial = null,
        public ?string $direccion = null,
        public ?string $ubigeo = null,    // código de ubicación geográfica (Perú)
        public ?string $distrito = null,
        public ?string $provincia = null,
        public ?string $departamento = null,
        public ?string $pais = 'PE',
    ) {
    }
}
