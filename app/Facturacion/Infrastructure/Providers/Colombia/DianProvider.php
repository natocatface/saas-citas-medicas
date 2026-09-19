<?php

namespace App\Facturacion\Infrastructure\Providers\Colombia;

use App\Facturacion\Domain\Contracts\ProveedorFacturacion;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;
use App\Facturacion\Domain\Result\ResultadoEmision;

/**
 * STUB Colombia - DIAN (Factura Electrónica de Venta, UBL 2.1 + CUFE).
 *
 * Ejemplo de cómo se agrega un país NUEVO: se crea esta clase implementando
 * ProveedorFacturacion y se registra en el ServiceProvider. No se modifica
 * ninguna línea del ERP, del dominio, ni del adaptador de Perú (OCP).
 *
 * Para completarlo: generar UBL 2.1 DIAN, calcular CUFE, firmar y enviar al
 * web service de la DIAN (o a un PSE/proveedor tecnológico autorizado).
 */
final class DianProvider implements ProveedorFacturacion
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function pais(): Pais
    {
        return Pais::CO;
    }

    public function emitirFactura(Comprobante $c): ResultadoEmision
    {
        return $this->noImplementado($c);
    }

    public function emitirNotaCredito(Comprobante $c): ResultadoEmision
    {
        return $this->noImplementado($c);
    }

    public function anularFactura(Comprobante $c, string $motivo): ResultadoEmision
    {
        return $this->noImplementado($c);
    }

    public function consultarEstado(Comprobante $c): ResultadoConsulta
    {
        return new ResultadoConsulta(EstadoComprobante::PENDIENTE, null, 'DIAN: adaptador pendiente de implementar');
    }

    private function noImplementado(Comprobante $c): ResultadoEmision
    {
        return ResultadoEmision::fallo(
            $c->numeroCompleto(),
            'Adaptador DIAN (Colombia) aún no implementado. Ver hoja de ruta - Fase 4.',
            reintentar: false,
        );
    }
}
