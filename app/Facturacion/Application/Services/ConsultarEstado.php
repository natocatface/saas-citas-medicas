<?php

namespace App\Facturacion\Application\Services;

use App\Facturacion\Application\RegistroProveedores;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;

/** CASO DE USO: consultar el estado de un comprobante ante el organismo. */
final class ConsultarEstado
{
    public function __construct(private readonly RegistroProveedores $registro)
    {
    }

    public function ejecutar(Comprobante $comprobante): ResultadoConsulta
    {
        return $this->registro->para($comprobante->pais)->consultarEstado($comprobante);
    }
}
