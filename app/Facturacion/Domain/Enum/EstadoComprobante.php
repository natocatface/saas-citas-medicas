<?php

namespace App\Facturacion\Domain\Enum;

/**
 * Ciclo de vida de un comprobante frente al organismo tributario.
 */
enum EstadoComprobante: string
{
    case BORRADOR = 'borrador';       // creado en el ERP, aún no enviado
    case PENDIENTE = 'pendiente';     // encolado para envío / reintento
    case ENVIADO = 'enviado';         // transmitido, esperando respuesta
    case ACEPTADO = 'aceptado';       // aceptado por el organismo (CDR conforme)
    case OBSERVADO = 'observado';     // aceptado con observaciones
    case RECHAZADO = 'rechazado';     // rechazado por el organismo
    case ANULADO = 'anulado';         // dado de baja / comunicación de baja aceptada
    case ERROR = 'error';             // fallo técnico (red, firma, etc.) reintentable
}
