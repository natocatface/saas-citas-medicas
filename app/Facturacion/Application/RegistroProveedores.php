<?php

namespace App\Facturacion\Application;

use App\Facturacion\Domain\Contracts\ProveedorFacturacion;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Exception\ProveedorNoSoportadoException;

/**
 * Resolutor del patrón Strategy: mapea País -> ProveedorFacturacion.
 *
 * Los adaptadores se auto-registran vía el ServiceProvider. Añadir un país es
 * `->registrar(new NuevoProvider())`, sin condicionales gigantes ni switch.
 */
final class RegistroProveedores
{
    /** @var array<string, ProveedorFacturacion> */
    private array $proveedores = [];

    public function registrar(ProveedorFacturacion $proveedor): void
    {
        $this->proveedores[$proveedor->pais()->value] = $proveedor;
    }

    public function para(Pais $pais): ProveedorFacturacion
    {
        return $this->proveedores[$pais->value]
            ?? throw ProveedorNoSoportadoException::para($pais);
    }

    public function soporta(Pais $pais): bool
    {
        return isset($this->proveedores[$pais->value]);
    }

    /** @return string[] Países habilitados actualmente. */
    public function paisesDisponibles(): array
    {
        return array_keys($this->proveedores);
    }
}
