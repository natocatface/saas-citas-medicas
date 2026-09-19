<?php

namespace App\Facturacion\Domain\Enum;

enum Moneda: string
{
    case PEN = 'PEN';
    case USD = 'USD';
    case COP = 'COP';
    case CLP = 'CLP';
    case ARS = 'ARS';
    case MXN = 'MXN';
}
