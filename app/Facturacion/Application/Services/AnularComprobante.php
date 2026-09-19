<?php

namespace App\Facturacion\Application\Services;

use App\Facturacion\Application\RegistroProveedores;
use App\Facturacion\Domain\Contracts\RepositorioComprobantes;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoEmision;

/** CASO DE USO: anular/dar de baja un comprobante ya emitido. */
final class AnularComprobante
{
    public function __construct(
        private readonly RegistroProveedores $registro,
        private readonly RepositorioComprobantes $repositorio,
    ) {
    }

    public function ejecutar(Comprobante $comprobante, string $motivo): ResultadoEmision
    {
        $resultado = $this->registro->para($comprobante->pais)->anularFactura($comprobante, $motivo);

        // El estado del comprobante sólo se persiste cuando la baja PROGRESA
        // (anulado o en proceso/pendiente). Un fallo —p. ej. una boleta que no
        // admite baja— NO debe sobrescribir el estado 'aceptado' del original.
        if (in_array($resultado->estado, [EstadoComprobante::ANULADO, EstadoComprobante::PENDIENTE], true)) {
            $this->repositorio->guardar($comprobante, $resultado);
        }

        $this->repositorio->registrarEvento($comprobante->numeroCompleto(), 'anulacion', [
            'motivo' => $motivo,
            'exito' => $resultado->exito,
            'estado' => $resultado->estado->value,
            'ticket' => $resultado->ticket,
            'mensaje' => $resultado->mensaje,
        ]);

        return $resultado;
    }
}
