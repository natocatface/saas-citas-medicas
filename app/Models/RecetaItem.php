<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetaItem extends Model
{
    use HasFactory;

    protected $table = 'receta_items';

    protected $fillable = ['receta_id', 'medicamento', 'dosis', 'frecuencia', 'duracion', 'indicaciones'];

    public function receta()
    {
        return $this->belongsTo(Receta::class);
    }
}
