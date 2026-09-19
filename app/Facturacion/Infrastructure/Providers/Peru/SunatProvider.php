<?php

namespace App\Facturacion\Infrastructure\Providers\Peru;

use App\Facturacion\Domain\Contracts\AlmacenDocumentos;
use App\Facturacion\Domain\Contracts\ProveedorFacturacion;
use App\Facturacion\Domain\Enum\EstadoComprobante;
use App\Facturacion\Domain\Enum\Pais;
use App\Facturacion\Domain\Enum\TipoComprobante;
use App\Facturacion\Domain\Exception\FacturacionException;
use App\Facturacion\Domain\Model\Comprobante;
use App\Facturacion\Domain\Result\ResultadoConsulta;
use App\Facturacion\Domain\Result\ResultadoEmision;
use Greenter\See;

/**
 * Adaptador Perú - SUNAT.
 *
 * Cada emisión resuelve las credenciales de LA clínica emisora (RUC, certificado,
 * clave SOL, entorno) desde su configuración. Si la clínica no habilitó la emisión
 * real o Greenter no está instalado, opera en modo SIMULADO.
 */
final class SunatProvider implements ProveedorFacturacion
{
    public function __construct(
        private readonly ResolverConfiguracionSunat $resolver,
        private readonly AlmacenDocumentos $almacen,
        private readonly ConstructorUblGreenter $constructor,
    ) {
    }

    public function pais(): Pais
    {
        return Pais::PE;
    }

    public function emitirFactura(Comprobante $c): ResultadoEmision
    {
        return $this->enviar($c);
    }

    public function emitirNotaCredito(Comprobante $c): ResultadoEmision
    {
        return $this->enviar($c);
    }

    public function anularFactura(Comprobante $c, string $motivo): ResultadoEmision
    {
        try {
            $cred = $this->credenciales($c);
        } catch (FacturacionException $e) {
            return ResultadoEmision::fallo($c->numeroCompleto(), $e->getMessage(), reintentar: false);
        }

        // Sin credenciales reales / Greenter -> baja simulada.
        if ($cred === null || ! class_exists(See::class)) {
            return $this->simular($c, EstadoComprobante::ANULADO, 'Baja simulada: '.$motivo);
        }

        try {
            $correlativoBaja = (string) $this->siguienteCorrelativoBaja();

            // Facturas -> Comunicación de Baja (RA). Boletas -> Resumen Diario (RC)
            // con estado 3 (dado de baja); las boletas no admiten RA individual.
            $r = $c->tipo === TipoComprobante::BOLETA
                ? $this->constructor->enviarBajaBoleta($cred, $c, $correlativoBaja)
                : $this->constructor->enviarBaja($cred, $c, $motivo, $correlativoBaja);
        } catch (\Throwable $e) {
            // Fallo técnico (red / SOAP): reintentable.
            return ResultadoEmision::fallo($c->numeroCompleto(), $e->getMessage(), reintentar: true);
        }

        // El XML/CDR de la baja se guardan en disco para trazabilidad, pero NO se
        // exponen como rutaXml/rutaCdr para no sobrescribir los del comprobante.
        if (! empty($r['xml'])) {
            $this->almacen->guardar($this->rutaBaja($c, $cred->ruc, $r['ra'], 'xml'), $r['xml']);
        }
        if (! empty($r['cdrXml'])) {
            $this->almacen->guardar($this->rutaBaja($c, $cred->ruc, $r['ra'], 'cdr'), $r['cdrXml']);
        }

        $estado = match ($r['estado']) {
            'anulado' => EstadoComprobante::ANULADO,
            'pendiente' => EstadoComprobante::PENDIENTE,
            default => EstadoComprobante::RECHAZADO,
        };

        return new ResultadoEmision(
            exito: (bool) $r['exito'],
            estado: $estado,
            numeroCompleto: $c->numeroCompleto(),
            codigoRespuesta: $r['codigo'] ?? null,
            mensaje: $r['mensaje'] ?? null,
            ticket: $r['ticket'] ?? null,
        );
    }

    /**
     * Correlativo diario de la Comunicación de Baja (RA). Se basa en el número de
     * anulaciones registradas hoy; es monótono y admite huecos, como acepta SUNAT.
     */
    private function siguienteCorrelativoBaja(): int
    {
        return \App\Facturacion\Infrastructure\Persistence\Models\ComprobanteEvento::query()
            ->where('evento', 'anulacion')
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;
    }

    private function rutaBaja(Comprobante $c, string $ruc, string $ra, string $tipo): string
    {
        $ext = $tipo === 'cdr' ? 'zip' : 'xml';

        return "facturacion/PE/{$ruc}/baja/{$ra}.{$ext}";
    }

    public function consultarEstado(Comprobante $c): ResultadoConsulta
    {
        $cred = $this->credenciales($c);
        if ($cred === null) {
            return new ResultadoConsulta(EstadoComprobante::ACEPTADO, '0', 'Estado simulado');
        }

        // Tras la emisión el CDR ya define el estado; aquí se refleja como aceptado.
        return new ResultadoConsulta(EstadoComprobante::ACEPTADO, '0', 'Comprobante transmitido a SUNAT');
    }

    /** Flujo de emisión: resuelve credenciales → real (Greenter) o simulado. */
    private function enviar(Comprobante $c): ResultadoEmision
    {
        try {
            $cred = $this->credenciales($c);
        } catch (FacturacionException $e) {
            // Configuración incompleta o certificado inválido: no reintentar.
            return ResultadoEmision::fallo($c->numeroCompleto(), $e->getMessage(), reintentar: false);
        }

        if ($cred === null || ! class_exists(See::class)) {
            return $this->simular($c, EstadoComprobante::ACEPTADO, 'Comprobante aceptado (modo simulado)');
        }

        try {
            $r = $this->constructor->emitir($cred, $c);
        } catch (\Throwable $e) {
            // Fallo técnico (red / SOAP): reintentable por el job.
            return ResultadoEmision::fallo($c->numeroCompleto(), $e->getMessage(), reintentar: true);
        }

        $rutaXml = $r['xml'] ? $this->almacen->guardar($this->rutaXml($c, $cred->ruc), $r['xml']) : null;
        $rutaCdr = $r['cdrXml'] ? $this->almacen->guardar($this->rutaCdr($c, $cred->ruc), $r['cdrXml']) : null;

        $observado = ! empty($r['observaciones']);
        $estado = $r['exito']
            ? ($observado ? EstadoComprobante::OBSERVADO : EstadoComprobante::ACEPTADO)
            : EstadoComprobante::RECHAZADO;

        return new ResultadoEmision(
            exito: (bool) $r['exito'],
            estado: $estado,
            numeroCompleto: $c->numeroCompleto(),
            codigoRespuesta: $r['codigo'],
            mensaje: $r['mensaje'],
            hashCpe: $r['hash'],
            rutaXml: $rutaXml,
            rutaCdr: $rutaCdr,
            observaciones: $r['observaciones'] ?? [],
            reintentar: false,
        );
    }

    private function credenciales(Comprobante $c): ?CredencialesSunat
    {
        $clinicaId = $c->metadata['clinica_id'] ?? null;

        return $this->resolver->paraClinica($clinicaId ? (int) $clinicaId : null);
    }

    private function simular(Comprobante $c, EstadoComprobante $estado, string $mensaje): ResultadoEmision
    {
        $xml = $this->xmlSimulado($c);
        $rutaXml = $this->almacen->guardar($this->rutaXml($c, $c->emisor->ruc), $xml);

        return new ResultadoEmision(
            exito: true,
            estado: $estado,
            numeroCompleto: $c->numeroCompleto(),
            codigoRespuesta: '0',
            mensaje: $mensaje,
            hashCpe: substr(hash('sha256', $xml), 0, 40),
            rutaXml: $rutaXml,
        );
    }

    private function xmlSimulado(Comprobante $c): string
    {
        $lineas = '';
        foreach ($c->lineas as $l) {
            $lineas .= sprintf("  <Item desc=\"%s\" cant=\"%.2f\" pu=\"%.2f\" igv=\"%.2f\"/>\n",
                htmlspecialchars($l->descripcion), $l->cantidad, $l->precioUnitario, $l->igv);
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
            "<Comprobante tipo=\"{$c->tipo->value}\" serie=\"{$c->serie}\" correlativo=\"{$c->correlativo}\">\n".
            "  <Emisor ruc=\"{$c->emisor->ruc}\" razonSocial=\"{$c->emisor->razonSocial}\"/>\n".
            "  <Receptor numero=\"{$c->receptor->numeroDocumento}\" nombre=\"{$c->receptor->razonSocial}\"/>\n".
            "  <Totales gravado=\"{$c->totales->gravado}\" igv=\"{$c->totales->igv}\" total=\"{$c->totales->total}\"/>\n".
            $lineas."</Comprobante>";
    }

    private function rutaXml(Comprobante $c, string $ruc): string
    {
        return "facturacion/PE/{$ruc}/xml/{$c->numeroCompleto()}.xml";
    }

    private function rutaCdr(Comprobante $c, string $ruc): string
    {
        return "facturacion/PE/{$ruc}/cdr/R-{$c->numeroCompleto()}.zip";
    }
}
