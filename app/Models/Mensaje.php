<?php
namespace App\Models;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Mensaje extends Model
{
    use HasFactory, BelongsToClinica;
    protected $table = 'mensajes';
    protected $fillable = ['clinica_id', 'remitente_id', 'destinatario_id', 'asunto', 'cuerpo', 'leido', 'leido_at'];
    protected $casts = ['leido' => 'boolean', 'leido_at' => 'datetime'];
    public function remitente() { return $this->belongsTo(User::class, 'remitente_id'); }
    public function destinatario() { return $this->belongsTo(User::class, 'destinatario_id'); }
}
