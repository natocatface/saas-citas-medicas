<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinica extends Model
{
    use HasFactory, Auditable;

    protected $table = 'clinicas';

    protected $fillable = [
        'nombre', 'slug', 'email', 'telefono', 'direccion',
        'plan_id', 'estado_suscripcion', 'suscripcion_vence', 'color', 'activo',
    ];

    protected $casts = [
        'suscripcion_vence' => 'date',
        'activo' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }

    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}
