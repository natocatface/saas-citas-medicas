<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaItem extends Model
{
    use HasFactory;

    protected $table = 'factura_items';

    protected $fillable = ['factura_id', 'descripcion', 'cantidad', 'precio_unitario', 'importe'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'importe' => 'decimal:2',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}
