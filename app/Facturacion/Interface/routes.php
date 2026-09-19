<?php

use App\Facturacion\Interface\Http\FacturacionApiController;
use Illuminate\Support\Facades\Route;

/**
 * API REST del servicio de facturación. Diseñada como contrato estable para que,
 * el día que el módulo se extraiga a un microservicio, el ERP siga consumiendo
 * exactamente los mismos endpoints (sólo cambia la URL base).
 */
Route::prefix('api/facturacion')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::post('/comprobantes', [FacturacionApiController::class, 'emitir']);
        Route::post('/comprobantes/{numero}/anular', [FacturacionApiController::class, 'anular']);
        Route::post('/notas-credito', [FacturacionApiController::class, 'notaCredito']);
        Route::get('/comprobantes/{numero}/estado', [FacturacionApiController::class, 'estado']);
    });
