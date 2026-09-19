<?php

namespace App\Facturacion\Domain\Enum;

/**
 * Documento de identidad del receptor, en términos de dominio.
 * Cada adaptador lo mapea a su catálogo local
 * (ej. Perú catálogo 06=RUC, 01=DNI; México=RFC; Chile=RUT...).
 */
enum TipoDocumentoIdentidad: string
{
    case RUC = 'ruc';   // contribuyente (empresa)
    case DNI = 'dni';   // persona natural
    case CE = 'ce';     // carné de extranjería / pasaporte
    case SIN = 'sin';   // sin identificación (consumidor final)
}
