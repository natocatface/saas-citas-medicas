<?php

namespace App\Facturacion\Application\Services;

use App\Facturacion\Application\RegistroProveedores;
use App\Facturacion\Domain\Contracts\RepositorioComprobantes;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoEmision;
use Psr\Log\LoggerInterface;

/**
 * CASO DE USO: emitir una factura/boleta electrónica.
 *
 * Orquesta: resuelve el proveedor por país -> emite -> persiste -> audita.
 * No conoce SUNAT ni ningún país concreto: todo pasa por los puertos.
 */
final class EmitirComprobante
{
    public function __construct(
        private readonly RegistroProveedores $registro,
        private readonly RepositorioComprobantes $repositorio,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function ejecutar(Comprobante $comprobante): ResultadoEmision
    {
        $proveedor = $this->registro->para($comprobante->pais);
        $numero = $comprobante->numeroCompleto();

        $this->repositorio->registrarEvento($numero, 'emision.iniciada', $comprobante->metadata);

        try {
            $resultado = $proveedor->emitirFactura($comprobante);
        } catch (\Throwable $e) {
            // Error técnico no controlado -> marcar reintentable, no perder el comprobante.
            $this->logger->error('Facturación: fallo al emitir', [
                'numero' => $numero, 'pais' => $comprobante->pais->value, 'error' => $e->getMessage(),
            ]);
            $resultado = ResultadoEmision::fallo($numero, $e->getMessage(), reintentar: true);
        }

        $this->repositorio->guardar($comprobante, $resultado);
        $this->repositorio->registrarEvento($numero, 'emision.resultado', [
            'exito' => $resultado->exito,
            'estado' => $resultado->estado->value,
            'codigo' => $resultado->codigoRespuesta,
            'mensaje' => $resultado->mensaje,
        ]);

        return $resultado;
    }
}
