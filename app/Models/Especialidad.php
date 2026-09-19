<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory, BelongsToClinica;

    protected $table = 'especialidades';

    protected $fillable = ['clinica_id', 'nombre', 'descripcion', 'color', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }
}
