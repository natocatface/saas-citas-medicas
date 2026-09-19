<?php

namespace App\Facturacion\Infrastructure\Providers\Mexico;

use App\Facturacion\Domain\Contracts\ProveedorFacturacion;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;
use App\Facturacion\Domain\Result\ResultadoEmision;

/**
 * STUB México - SAT (CFDI 4.0). El timbrado se realiza mediante un PAC
 * (Proveedor Autorizado de Certificación). Para completar: generar el CFDI XML,
 * sellar con el CSD, enviar al PAC y guardar el Timbre Fiscal Digital (UUID).
 */
final class SatProvider implements ProveedorFacturacion
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function pais(): Pais
    {
        return Pais::MX;
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
        return new ResultadoConsulta(EstadoComprobante::PENDIENTE, null, 'SAT: adaptador pendiente de implementar');
    }

    private function noImplementado(Comprobante $c): ResultadoEmision
    {
        return ResultadoEmision::fallo($c->numeroCompleto(), 'Adaptador SAT (México / CFDI) aún no implementado. Ver hoja de ruta - Fase 6.', false);
    }
}
