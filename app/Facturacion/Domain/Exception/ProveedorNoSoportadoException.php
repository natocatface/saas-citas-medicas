<?php

namespace App\Facturacion\Domain\Exception;

use App\Facturacion\Domain\Enum\Pais;

class ProveedorNoSoportadoException extends FacturacionException
{
    public static function para(Pais $pais): self
    {
        return new self("No hay proveedor de facturación registrado para {$pais->value} ({$pais->organismo()}).");
    }
}
