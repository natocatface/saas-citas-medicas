<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory, BelongsToClinica, Auditable;

    protected $table = 'facturas';

    protected $fillable = [
        'clinica_id', 'numero', 'paciente_id', 'cita_id', 'fecha',
        'subtotal', 'descuento', 'impuesto', 'total',
        'estado', 'metodo_pago', 'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function items()
    {
        return $this->hasMany(FacturaItem::class);
    }

    /**
     * Comprobante electrónico (SUNAT/DIAN/...) generado a partir de esta factura.
     * Vive en el contexto acotado App\Facturacion; el ERP sólo consulta su estado.
     */
    public function comprobanteElectronico()
    {
        return $this->hasOne(
            \App\Facturacion\Infrastructure\Persistence\Models\ComprobanteElectronico::class,
            'factura_id'
        )->latestOfMany();
    }
}
