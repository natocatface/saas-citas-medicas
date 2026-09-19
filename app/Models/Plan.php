<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'planes';

    protected $fillable = [
        'nombre', 'precio', 'periodo', 'max_medicos', 'max_usuarios',
        'caracteristicas', 'destacado', 'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'destacado' => 'boolean',
        'activo' => 'boolean',
    ];

    public function clinicas()
    {
        return $this->hasMany(Clinica::class);
    }

    public function caracteristicasLista(): array
    {
        return array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->caracteristicas)));
    }
}
