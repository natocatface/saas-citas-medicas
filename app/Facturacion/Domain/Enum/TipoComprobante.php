<?php

namespace App\Facturacion\Domain\Enum;

/**
 * Tipo de comprobante en lenguaje de dominio (agnóstico al país).
 * Cada adaptador traduce estos valores al catálogo local
 * (ej. en Perú: 01=Factura, 03=Boleta, 07=NC, 08=ND).
 */
enum TipoComprobante: string
{
    case FACTURA = 'factura';
    case BOLETA = 'boleta';           // consumidor final (equivalente a ticket/consumidor final)
    case NOTA_CREDITO = 'nota_credito';
    case NOTA_DEBITO = 'nota_debito';
}
