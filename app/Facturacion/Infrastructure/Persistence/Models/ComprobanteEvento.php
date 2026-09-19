<?php

namespace App\Facturacion\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Bitácora de auditoría append-only: cada paso del ciclo de vida de un
 * comprobante (iniciada, enviada, aceptada, error, baja...) queda registrado.
 */
class ComprobanteEvento extends Model
{
    protected $table = 'comprobante_eventos';

    protected $fillable = ['comprobante_id', 'numero_completo', 'evento', 'payload'];

    protected $casts = ['payload' => 'array'];

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(ComprobanteElectronico::class, 'comprobante_id');
    }
}
