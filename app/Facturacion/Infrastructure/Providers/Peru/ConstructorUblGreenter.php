<?php

namespace App\Facturacion\Infrastructure\Providers\Peru;

use App\Facturacion\Domain\Enum\TipoComprobante;
use App\Facturacion\Domain\Enum\TipoDocumentoIdentidad;
use App\Facturacion\Domain\Model\Comprobante;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\See;

/**
 * Único punto que conoce Greenter. Traduce el Comprobante de dominio a los
 * modelos de Greenter, firma (XAdES-BES) y transmite a SUNAT vía SOAP.
 *
 * Se activa sólo cuando la dependencia `greenter/lite` está instalada y la
 * clínica tiene credenciales válidas (ver ResolverConfiguracionSunat).
 */
final class ConstructorUblGreenter
{
    /**
     * Emite el comprobante ante SUNAT.
     *
     * @return array{exito:bool,codigo:?string,mensaje:?string,xml:?string,cdrXml:?string,hash:?string,qr:?string,observaciones:array}
     */
    public function emitir(CredencialesSunat $cred, Comprobante $c): array
    {
        $see = $this->cliente($cred);
        $documento = $c->tipo === TipoComprobante::NOTA_CREDITO
            ? $this->construirNota($cred, $c)
            : $this->construirFactura($cred, $c);

        $xmlFirmado = $see->getXmlSigned($documento); // firma XAdES-BES
        $hash = $this->extraerDigest($xmlFirmado);
        $resultado = $see->send($documento);

        if (! $resultado->isSuccess()) {
            $err = $resultado->getError();

            return [
                'exito' => false,
                'codigo' => $err?->getCode(),
                'mensaje' => $err?->getMessage() ?: 'Rechazado por SUNAT',
                'xml' => $xmlFirmado,
                'cdrXml' => null,
                'hash' => $hash,
                'qr' => $this->qr($cred, $c, $hash),
                'observaciones' => [],
            ];
        }

        $cdr = $resultado->getCdrResponse();

        return [
            'exito' => (int) $cdr->getCode() === 0,
            'codigo' => (string) $cdr->getCode(),
            'mensaje' => $cdr->getDescription(),
            'xml' => $xmlFirmado,
            'cdrXml' => $this->extraerCdrXml($resultado),
            'hash' => $hash,
            'qr' => $this->qr($cred, $c, $hash),
            'observaciones' => $cdr->getNotes() ?? [],
        ];
    }

    /**
     * Envía una COMUNICACIÓN DE BAJA (Voided Documents, RA) a SUNAT para anular
     * una factura ya emitida. El proceso es asíncrono: SUNAT devuelve un ticket
     * y luego se consulta el estado (0=aceptado, 98=en proceso, 99=rechazado).
     *
     * @return array{exito:bool,estado:string,codigo:?string,mensaje:?string,ticket:?string,ra:string,xml:?string,cdrXml:?string}
     */
    public function enviarBaja(CredencialesSunat $cred, Comprobante $c, string $motivo, string $correlativoBaja): array
    {
        $see = $this->cliente($cred);

        $voided = (new Voided())
            ->setCorrelativo($correlativoBaja)
            ->setFecGeneracion(new \DateTime($c->fechaEmision->format('Y-m-d')))
            ->setFecComunicacion(new \DateTime('now'))
            ->setCompany($this->company($cred))
            ->setDetails([
                (new VoidedDetail())
                    ->setTipoDoc($this->tipoDocumentoSunat($c->tipo))
                    ->setSerie($c->serie)
                    ->setCorrelativo(ltrim($c->correlativo, '0') ?: '0')
                    ->setDesMotivoBaja($motivo),
            ]);

        $xmlFirmado = $see->getXmlSigned($voided);
        $res = $see->send($voided); // SummaryResult (con ticket)

        if (! $res || ! $res->isSuccess()) {
            $err = $res?->getError();

            return [
                'exito' => false,
                'estado' => 'rechazado',
                'codigo' => $err?->getCode(),
                'mensaje' => $err?->getMessage() ?: 'SUNAT rechazó la comunicación de baja',
                'ticket' => null,
                'ra' => $voided->getName(),
                'xml' => $xmlFirmado,
                'cdrXml' => null,
            ];
        }

        $ticket = $res->getTicket();
        $estado = $this->consultarTicket($see, $ticket);

        return array_merge($estado, [
            'ticket' => $ticket,
            'ra' => $voided->getName(),
            'xml' => $xmlFirmado,
        ]);
    }

    /**
     * Anula una BOLETA ya emitida mediante el RESUMEN DIARIO (RC) con estado 3
     * (dado de baja). Las boletas no admiten Comunicación de Baja (RA); su
     * anulación se comunica en un resumen. También es asíncrono (ticket).
     *
     * @return array{exito:bool,estado:string,codigo:?string,mensaje:?string,ticket:?string,ra:string,xml:?string,cdrXml:?string}
     */
    public function enviarBajaBoleta(CredencialesSunat $cred, Comprobante $c, string $correlativoResumen): array
    {
        $see = $this->cliente($cred);

        $porc = $c->totales->gravado > 0 ? round($c->totales->igv / $c->totales->gravado * 100) : 18;

        $detalle = (new SummaryDetail())
            ->setTipoDoc($this->tipoDocumentoSunat($c->tipo))                  // 03 boleta
            ->setSerieNro($c->serie.'-'.(ltrim($c->correlativo, '0') ?: '0'))  // ej. B001-1
            ->setEstado('3')                                                  // 3 = anulado (baja)
            ->setClienteTipo($this->tipoDocIdentidadSunat($c->receptor->tipoDocumento))
            ->setClienteNro($c->receptor->numeroDocumento)
            ->setTotal($c->totales->total)
            ->setMtoOperGravadas($c->totales->gravado)
            ->setPorcentajeIgv((float) $porc)
            ->setMtoIGV($c->totales->igv);

        $summary = (new Summary())
            ->setCorrelativo($correlativoResumen)
            ->setFecGeneracion(new \DateTime($c->fechaEmision->format('Y-m-d')))
            ->setFecResumen(new \DateTime('now'))
            ->setMoneda($c->moneda->value)
            ->setCompany($this->company($cred))
            ->setDetails([$detalle]);

        $xmlFirmado = $see->getXmlSigned($summary);
        $res = $see->send($summary); // SummaryResult (con ticket)

        if (! $res || ! $res->isSuccess()) {
            $err = $res?->getError();

            return [
                'exito' => false,
                'estado' => 'rechazado',
                'codigo' => $err?->getCode(),
                'mensaje' => $err?->getMessage() ?: 'SUNAT rechazó el resumen de baja',
                'ticket' => null,
                'ra' => $summary->getName(),
                'xml' => $xmlFirmado,
                'cdrXml' => null,
            ];
        }

        $ticket = $res->getTicket();
        $estado = $this->consultarTicket($see, $ticket);

        return array_merge($estado, [
            'ticket' => $ticket,
            'ra' => $summary->getName(),
            'xml' => $xmlFirmado,
        ]);
    }

    /**
     * Consulta el estado de un ticket de baja/resumen ya enviado.
     *
     * @return array{exito:bool,estado:string,codigo:?string,mensaje:?string,cdrXml:?string}
     */
    public function consultarBaja(CredencialesSunat $cred, string $ticket): array
    {
        return $this->consultarTicket($this->cliente($cred), $ticket, intentos: 1, esperaSeg: 0);
    }

    /**
     * Sondea el ticket varias veces (SUNAT procesa la baja de forma asíncrona).
     *
     * @return array{exito:bool,estado:string,codigo:?string,mensaje:?string,cdrXml:?string}
     */
    private function consultarTicket(See $see, ?string $ticket, int $intentos = 3, int $esperaSeg = 2): array
    {
        for ($i = 0; $i < $intentos; $i++) {
            if ($esperaSeg > 0) {
                sleep($esperaSeg);
            }

            $st = $see->getStatus($ticket); // StatusResult
            $code = $st->getCode();

            if ($code === '0') {
                $cdr = $st->getCdrResponse();

                return [
                    'exito' => true,
                    'estado' => 'anulado',
                    'codigo' => (string) ($cdr?->getCode() ?? '0'),
                    'mensaje' => $cdr?->getDescription() ?: 'Baja aceptada por SUNAT',
                    'cdrXml' => method_exists($st, 'getCdrZip') ? $st->getCdrZip() : null,
                ];
            }

            if ($code === '99') {
                $err = $st->getError();

                return [
                    'exito' => false,
                    'estado' => 'rechazado',
                    'codigo' => $err?->getCode() ?: '99',
                    'mensaje' => $err?->getMessage() ?: 'SUNAT rechazó la baja',
                    'cdrXml' => null,
                ];
            }
            // code 98 = en proceso -> se reintenta.
        }

        return [
            'exito' => false,
            'estado' => 'pendiente',
            'codigo' => '98',
            'mensaje' => 'Baja en proceso en SUNAT. Consulta el estado con el ticket en unos minutos.',
            'cdrXml' => null,
        ];
    }

    private function cliente(CredencialesSunat $cred): See
    {
        $see = new See();
        $see->setCertificate($cred->certificadoPem);
        $see->setService($cred->endpoint);
        $see->setClaveSOL($cred->ruc, $cred->solUsuario, $cred->solClave);

        return $see;
    }

    private function construirFactura(CredencialesSunat $cred, Comprobante $c): Invoice
    {
        return (new Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')                       // venta interna
            ->setTipoDoc($this->tipoDocumentoSunat($c->tipo)) // 01 factura / 03 boleta
            ->setSerie($c->serie)
            ->setCorrelativo(ltrim($c->correlativo, '0') ?: '0')
            ->setFechaEmision(new \DateTime($c->fechaEmision->format('Y-m-d\TH:i:sP')))
            ->setFormaPago(new FormaPagoContado())
            ->setTipoMoneda($c->moneda->value)
            ->setCompany($this->company($cred))
            ->setClient($this->client($c))
            ->setMtoOperGravadas($c->totales->gravado)
            ->setMtoIGV($c->totales->igv)
            ->setTotalImpuestos($c->totales->igv)
            ->setValorVenta($c->totales->gravado)
            ->setSubTotal($c->totales->total)
            ->setMtoImpVenta($c->totales->total)
            ->setDetails($this->detalles($c))
            ->setLegends([
                (new Legend())->setCode('1000')->setValue($this->montoEnLetras($c->totales->total)),
            ]);
    }

    private function construirNota(CredencialesSunat $cred, Comprobante $c): Note
    {
        $afectado = $c->documentosRelacionados[0] ?? '';

        return (new Note())
            ->setUblVersion('2.1')
            ->setTipoDoc('07')                               // nota de crédito
            ->setSerie($c->serie)
            ->setCorrelativo(ltrim($c->correlativo, '0') ?: '0')
            ->setFechaEmision(new \DateTime($c->fechaEmision->format('Y-m-d\TH:i:sP')))
            ->setTipDocAfectado('01')
            ->setNumDocfectado($afectado)
            ->setCodMotivo($c->codigoMotivoNota ?: '01')
            ->setDesMotivo($c->motivoNota ?: 'ANULACION DE LA OPERACION')
            ->setTipoMoneda($c->moneda->value)
            ->setCompany($this->company($cred))
            ->setClient($this->client($c))
            ->setMtoOperGravadas($c->totales->gravado)
            ->setMtoIGV($c->totales->igv)
            ->setTotalImpuestos($c->totales->igv)
            ->setMtoImpVenta($c->totales->total)
            ->setDetails($this->detalles($c))
            ->setLegends([
                (new Legend())->setCode('1000')->setValue($this->montoEnLetras($c->totales->total)),
            ]);
    }

    private function company(CredencialesSunat $cred): Company
    {
        return (new Company())
            ->setRuc($cred->ruc)
            ->setRazonSocial($cred->razonSocial)
            ->setNombreComercial($cred->nombreComercial ?? $cred->razonSocial)
            ->setAddress(
                (new Address())
                    ->setUbigueo($cred->ubigeo ?: '150101')
                    ->setDepartamento($cred->departamento ?: 'LIMA')
                    ->setProvincia($cred->provincia ?: 'LIMA')
                    ->setDistrito($cred->distrito ?: 'LIMA')
                    ->setDireccion($cred->direccion ?: '-')
            );
    }

    private function client(Comprobante $c): Client
    {
        return (new Client())
            ->setTipoDoc($this->tipoDocIdentidadSunat($c->receptor->tipoDocumento))
            ->setNumDoc($c->receptor->numeroDocumento)
            ->setRznSocial($c->receptor->razonSocial);
    }

    /** @return SaleDetail[] */
    private function detalles(Comprobante $c): array
    {
        $items = [];
        foreach ($c->lineas as $l) {
            $base = $l->importe;
            $igv = $l->igv;
            $porc = $base > 0 ? round($igv / $base * 100) : 18;

            $items[] = (new SaleDetail())
                ->setCodProducto($l->codigoProducto ?: null)
                ->setUnidad($l->unidadMedida ?: 'NIU')
                ->setCantidad($l->cantidad)
                ->setDescripcion($l->descripcion)
                ->setMtoValorUnitario($l->precioUnitario)
                ->setMtoValorVenta($base)
                ->setMtoBaseIgv($base)
                ->setPorcentajeIgv($porc)
                ->setIgv($igv)
                ->setTipAfeIgv('10')                     // gravado - operación onerosa
                ->setTotalImpuestos($igv)
                ->setMtoPrecioUnitario($l->cantidad > 0 ? ($base + $igv) / $l->cantidad : ($l->precioUnitario * 1.18));
        }

        return $items;
    }

    // ---------- Catálogos SUNAT ----------

    public function tipoDocumentoSunat(TipoComprobante $t): string
    {
        return match ($t) {
            TipoComprobante::FACTURA => '01',
            TipoComprobante::BOLETA => '03',
            TipoComprobante::NOTA_CREDITO => '07',
            TipoComprobante::NOTA_DEBITO => '08',
        };
    }

    public function tipoDocIdentidadSunat(TipoDocumentoIdentidad $t): string
    {
        return match ($t) {
            TipoDocumentoIdentidad::RUC => '6',
            TipoDocumentoIdentidad::DNI => '1',
            TipoDocumentoIdentidad::CE => '4',
            TipoDocumentoIdentidad::SIN => '0',
        };
    }

    // ---------- Utilidades ----------

    /** Cadena del código QR según especificación SUNAT. */
    private function qr(CredencialesSunat $cred, Comprobante $c, ?string $hash): string
    {
        return implode('|', [
            $cred->ruc,
            $this->tipoDocumentoSunat($c->tipo),
            $c->serie,
            $c->correlativo,
            number_format($c->totales->igv, 2, '.', ''),
            number_format($c->totales->total, 2, '.', ''),
            $c->fechaEmision->format('Y-m-d'),
            $this->tipoDocIdentidadSunat($c->receptor->tipoDocumento),
            $c->receptor->numeroDocumento,
            $hash ?? '',
        ]);
    }

    private function extraerDigest(?string $xml): ?string
    {
        if (! $xml) {
            return null;
        }
        if (preg_match('/<ds:DigestValue>([^<]+)<\/ds:DigestValue>/', $xml, $m)) {
            return $m[1];
        }

        return null;
    }

    private function extraerCdrXml($resultado): ?string
    {
        // El CDR viene como ZIP; se guarda tal cual para trazabilidad.
        return method_exists($resultado, 'getCdrZip') ? $resultado->getCdrZip() : null;
    }

    private function montoEnLetras(float $monto): string
    {
        $entero = (int) floor($monto + 0.00001);
        $cent = (int) round(($monto - $entero) * 100);

        return sprintf('SON %s CON %02d/100 SOLES', $this->numeroALetras($entero), $cent);
    }

    private function numeroALetras(int $n): string
    {
        if ($n === 0) {
            return 'CERO';
        }
        if ($n >= 1000000) {
            return (string) $n;
        }
        $u = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
        $e = [10 => 'DIEZ', 11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE', 15 => 'QUINCE', 16 => 'DIECISEIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE'];
        $v = ['', 'VEINTIUNO', 'VEINTIDOS', 'VEINTITRES', 'VEINTICUATRO', 'VEINTICINCO', 'VEINTISEIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE'];
        $d = [3 => 'TREINTA', 4 => 'CUARENTA', 5 => 'CINCUENTA', 6 => 'SESENTA', 7 => 'SETENTA', 8 => 'OCHENTA', 9 => 'NOVENTA'];
        $ci = [1 => 'CIENTO', 2 => 'DOSCIENTOS', 3 => 'TRESCIENTOS', 4 => 'CUATROCIENTOS', 5 => 'QUINIENTOS', 6 => 'SEISCIENTOS', 7 => 'SETECIENTOS', 8 => 'OCHOCIENTOS', 9 => 'NOVECIENTOS'];

        $men = function (int $x) use ($u, $e, $v, $d, $ci): string {
            $o = '';
            $c = intdiv($x, 100);
            $r = $x % 100;
            if ($c > 0) {
                $o .= ($x === 100) ? 'CIEN' : $ci[$c];
            }
            if ($r > 0) {
                if ($o !== '') {
                    $o .= ' ';
                }
                if ($r < 10) {
                    $o .= $u[$r];
                } elseif ($r < 20) {
                    $o .= $e[$r];
                } elseif ($r < 30) {
                    $o .= $v[$r - 20];
                } else {
                    $dd = intdiv($r, 10);
                    $uu = $r % 10;
                    $o .= $d[$dd].($uu > 0 ? ' Y '.$u[$uu] : '');
                }
            }

            return $o;
        };

        $miles = intdiv($n, 1000);
        $resto = $n % 1000;
        $txt = '';
        if ($miles > 0) {
            $txt = ($miles === 1 ? 'MIL' : $men($miles).' MIL');
        }
        if ($resto > 0) {
            $txt = trim($txt.' '.$men($resto));
        }

        return $txt;
    }
}
