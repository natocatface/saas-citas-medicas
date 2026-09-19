<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory, BelongsToClinica, Auditable;

    protected $table = 'recetas';

    protected $fillable = [
        'clinica_id', 'numero', 'paciente_id', 'medico_id', 'cita_id', 'fecha',
        'diagnostico', 'indicaciones', 'notas',
    ];

    protected $casts = ['fecha' => 'date'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function items()
    {
        return $this->hasMany(RecetaItem::class);
    }
}
