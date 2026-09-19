<?php

namespace App\Facturacion\Infrastructure\Jobs;

use App\Facturacion\Application\Services\EmitirComprobante;
use App\Facturacion\Domain\Model\Comprobante;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Emisión ASÍNCRONA con reintentos automáticos.
 *
 * El ERP encola este job y responde al usuario al instante; la comunicación con
 * el organismo (que puede tardar o caerse) ocurre en segundo plano. Si el
 * resultado es reintentable (error técnico/red), se re-encola con backoff
 * exponencial hasta agotar $tries.
 */
final class EmitirComprobanteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Número de intentos antes de mandar a la cola de fallidos. */
    public int $tries = 5;

    /** Backoff exponencial en segundos entre reintentos. */
    public array $backoff = [30, 120, 300, 900, 3600];

    public function __construct(public readonly Comprobante $comprobante)
    {
        $this->onQueue('facturacion');
    }

    public function handle(EmitirComprobante $emitir): void
    {
        $resultado = $emitir->ejecutar($this->comprobante);

        // Sólo se reintenta ante fallos técnicos; un rechazo del organismo es final.
        if ($resultado->reintentar) {
            $this->release($this->backoff[$this->attempts() - 1] ?? 3600);
        }
    }
}
