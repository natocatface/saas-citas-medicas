<?php

namespace App\Facturacion\Infrastructure\Providers\Argentina;

use App\Facturacion\Domain\Contracts\ProveedorFacturacion;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;
use App\Facturacion\Domain\Result\ResultadoEmision;

/**
 * STUB Argentina - ARCA (ex AFIP). Facturación por WSFE (Web Service de
 * Factura Electrónica) que devuelve un CAE (Código de Autorización Electrónico).
 * Para completar: autenticar con WSAA (ticket TA), invocar FECAESolicitar y
 * guardar el CAE + su vencimiento.
 */
final class ArcaProvider implements ProveedorFacturacion
{
    public function __construct(private readonly array $config = [])
    {
    }

    public function pais(): Pais
    {
        return Pais::AR;
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
        return new ResultadoConsulta(EstadoComprobante::PENDIENTE, null, 'ARCA: adaptador pendiente de implementar');
    }

    private function noImplementado(Comprobante $c): ResultadoEmision
    {
        return ResultadoEmision::fallo($c->numeroCompleto(), 'Adaptador ARCA (Argentina) aún no implementado. Ver hoja de ruta - Fase 5.', false);
    }
}
