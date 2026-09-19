<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaEspera extends Model
{
    use HasFactory, BelongsToClinica;

    protected $table = 'lista_espera';

    protected $fillable = [
        'clinica_id', 'paciente_id', 'especialidad_id', 'medico_id',
        'prioridad', 'estado', 'motivo', 'notas',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
