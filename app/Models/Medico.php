<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory, BelongsToClinica;

    protected $table = 'medicos';

    protected $fillable = [
        'clinica_id', 'nombres', 'apellidos', 'documento', 'email', 'telefono',
        'especialidad_id', 'numero_colegiatura', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "Dr. {$this->nombres} {$this->apellidos}";
    }
}
