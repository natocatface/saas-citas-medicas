<?php

namespace App\Facturacion\Application\Services;

use App\Facturacion\Application\RegistroProveedores;
use App\Facturacion\Domain\Contracts\RepositorioComprobantes;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoEmision;

/** CASO DE USO: emitir una nota de crédito. */
final class EmitirNotaCredito
{
    public function __construct(
        private readonly RegistroProveedores $registro,
        private readonly RepositorioComprobantes $repositorio,
    ) {
    }

    public function ejecutar(Comprobante $notaCredito): ResultadoEmision
    {
        $resultado = $this->registro->para($notaCredito->pais)->emitirNotaCredito($notaCredito);

        $this->repositorio->guardar($notaCredito, $resultado);
        $this->repositorio->registrarEvento($notaCredito->numeroCompleto(), 'nota_credito', [
            'afecta' => $notaCredito->documentosRelacionados,
            'estado' => $resultado->estado->value,
        ]);

        return $resultado;
    }
}
