<?php
namespace App\Models;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PlantillaEmail extends Model
{
    use HasFactory, BelongsToClinica;
    protected $table = 'plantillas_email';
    protected $fillable = ['clinica_id', 'nombre', 'tipo', 'asunto', 'cuerpo', 'activo'];
    protected $casts = ['activo' => 'boolean'];
}
