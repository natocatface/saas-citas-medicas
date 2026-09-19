<?php

namespace App\Facturacion\Infrastructure\Providers\Chile;

use App\Facturacion\Domain\Contracts\ProveedorFacturacion;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;
use App\Facturacion\Domain\Result\ResultadoEmision;

/**
 * STUB Chile - SII (Documento Tributario Electrónico, formato DTE propio + folios CAF).
 * Para completar: generar el DTE XML, timbrar con folio CAF, firmar y enviar al SII.
 */
final class SiiProvider implements ProveedorFacturacion
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function pais(): Pais
    {
        return Pais::CL;
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
        return new ResultadoConsulta(EstadoComprobante::PENDIENTE, null, 'SII: adaptador pendiente de implementar');
    }

    private function noImplementado(Comprobante $c): ResultadoEmision
    {
        return ResultadoEmision::fallo($c->numeroCompleto(), 'Adaptador SII (Chile) aún no implementado. Ver hoja de ruta - Fase 5.', false);
    }
}
