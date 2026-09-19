<?php

namespace App\Facturacion\Domain\Contracts;

use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;
use App\Facturacion\Domain\Result\ResultadoEmision;

/**
 * PUERTO PRINCIPAL (patrón Strategy/Adapter).
 *
 * Es la interfaz común que TODOS los países implementan. El ERP y los casos de
 * uso sólo conocen esta abstracción; nunca una implementación concreta.
 *
 * Agregar un país nuevo = crear una clase que implemente esta interfaz y
 * registrarla en el RegistroProveedores. NO se toca ninguna línea del ERP
 * ni de los demás adaptadores (Open/Closed Principle).
 */
interface ProveedorFacturacion
{
    /** País que atiende este adaptador (clave de resolución en el registro). */
    public function pais(): Pais;

    /** Emite una factura/boleta y la transmite al organismo tributario. */
    public function emitirFactura(Comprobante $comprobante): ResultadoEmision;

    /**
     * Anula/da de baja un comprobante ya emitido.
     * (Perú: Comunicación de Baja; otros países: su equivalente).
     */
    public function anularFactura(Comprobante $comprobante, string $motivo): ResultadoEmision;

    /** Emite una nota de crédito asociada a uno o más comprobantes. */
    public function emitirNotaCredito(Comprobante $notaCredito): ResultadoEmision;

    /** Consulta el estado actual de un comprobante ante el organismo. */
    public function consultarEstado(Comprobante $comprobante): ResultadoConsulta;
}
