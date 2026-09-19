<?php
namespace App\Models;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Cie10 extends Model
{
    use HasFactory, BelongsToClinica;
    protected $table = 'cie10';
    protected $fillable = ['clinica_id', 'codigo', 'descripcion', 'categoria', 'activo'];
    protected $casts = ['activo' => 'boolean'];
}
