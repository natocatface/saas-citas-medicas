<?php

namespace App\Facturacion\Domain\Result;

use App\Facturacion\Domain\Enum\EstadoComprobante;

/**
 * Resultado de emitir / anular / notar un comprobante ante el organismo.
 * Es uniforme para todos los países: el ERP nunca ve estructuras de SUNAT/DIAN.
 */
final readonly class ResultadoEmision
{
    public function __construct(
        public bool $exito,
        public EstadoComprobante $estado,
        public string $numeroCompleto,        // serie-correlativo
        public ?string $codigoRespuesta = null, // ej. código CDR / código de error del organismo
        public ?string $mensaje = null,
        public ?string $hashCpe = null,         // hash/digest del comprobante firmado
        public ?string $rutaXml = null,         // ubicación del XML firmado almacenado
        public ?string $rutaCdr = null,         // ubicación de la constancia (CDR/acuse)
        public ?string $rutaPdf = null,
        public array $observaciones = [],       // notas/observaciones del organismo
        public bool $reintentar = false,        // el orquestador debe reintentar (error técnico)
        public ?string $ticket = null,          // ticket SUNAT para procesos asíncronos (baja/resumen)
    ) {
    }

    public static function ok(string $numero, EstadoComprobante $estado, array $extra = []): self
    {
        return new self(exito: true, estado: $estado, numeroCompleto: $numero, ...$extra);
    }

    public static function fallo(string $numero, string $mensaje, bool $reintentar = false, ?string $codigo = null): self
    {
        return new self(
            exito: false,
            estado: $reintentar ? EstadoComprobante::ERROR : EstadoComprobante::RECHAZADO,
            numeroCompleto: $numero,
            codigoRespuesta: $codigo,
            mensaje: $mensaje,
            reintentar: $reintentar,
        );
    }
}
