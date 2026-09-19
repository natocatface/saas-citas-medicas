<?php

namespace App\Facturacion\Infrastructure\Providers\Peru;

/**
 * Credenciales y datos de emisión de UNA empresa (clínica) para SUNAT.
 * Se arma en tiempo de emisión a partir de su FacturacionConfiguracion,
 * de modo que cada empresa emite con su propio RUC, certificado y clave SOL.
 */
final readonly class CredencialesSunat
{
    public function __construct(
        public string $ruc,
        public string $razonSocial,
        public ?string $nombreComercial,
        public ?string $direccion,
        public ?string $ubigeo,
        public ?string $departamento,
        public ?string $provincia,
        public ?string $distrito,
        public string $endpoint,        // URL del web service SUNAT (beta o producción)
        public string $solUsuario,
        public string $solClave,
        public string $certificadoPem,  // contenido PEM (llave privada + certificado)
        public string $modo,            // beta | produccion
    ) {
    }

    public function esProduccion(): bool
    {
        return $this->modo === 'produccion';
    }
}
