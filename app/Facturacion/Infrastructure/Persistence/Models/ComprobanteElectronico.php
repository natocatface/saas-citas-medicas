<?php

namespace App\Facturacion\Infrastructure\Persistence\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Registro persistente del comprobante electrónico y su estado tributario.
 * Reutiliza el trait multi-tenant del ERP (filtro por clinica_id).
 */
class ComprobanteElectronico extends Model
{
    use BelongsToClinica;

    protected $table = 'comprobantes_electronicos';

    protected $fillable = [
        'clinica_id', 'factura_id', 'pais', 'tipo', 'serie', 'correlativo', 'numero_completo',
        'moneda', 'emisor_ruc', 'receptor_doc', 'receptor_numero', 'receptor_nombre',
        'fecha_emision', 'gravado', 'igv', 'total',
        'estado', 'codigo_respuesta', 'mensaje', 'hash_cpe', 'ticket',
        'ruta_xml', 'ruta_cdr', 'ruta_pdf', 'observaciones', 'intentos',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'gravado' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'observaciones' => 'array',
        'intentos' => 'integer',
    ];

    public function eventos(): HasMany
    {
        return $this->hasMany(ComprobanteEvento::class, 'comprobante_id');
    }
}
