<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory, BelongsToClinica, Auditable;

    protected $table = 'pacientes';

    protected $fillable = [
        'clinica_id', 'nombres', 'apellidos', 'documento', 'fecha_nacimiento', 'sexo',
        'email', 'telefono', 'direccion', 'aseguradora_id', 'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    public function aseguradora()
    {
        return $this->belongsTo(Aseguradora::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
