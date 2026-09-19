<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aseguradora extends Model
{
    use HasFactory, BelongsToClinica;

    protected $table = 'aseguradoras';

    protected $fillable = ['clinica_id', 'nombre', 'ruc', 'telefono', 'email', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }
}
