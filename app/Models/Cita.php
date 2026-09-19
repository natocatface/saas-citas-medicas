<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory, BelongsToClinica, Auditable;

    protected $table = 'citas';

    protected $fillable = [
        'clinica_id', 'paciente_id', 'medico_id', 'fecha', 'hora', 'estado',
        'motivo', 'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
