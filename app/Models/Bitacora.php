<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'activity_log';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'clinica_id', 'usuario_nombre', 'accion',
        'descripcion', 'modelo', 'modelo_id', 'ip', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }

    /** Registra una acción. No hace nada en consola/seeders (sin usuario autenticado). */
    public static function registrar(string $accion, ?string $modeloId = null, ?string $modelo = null, ?string $descripcion = null): void
    {
        $u = auth()->user();
        if (! $u) {
            return;
        }
        static::create([
            'user_id' => $u->id,
            'clinica_id' => $u->clinica_id,
            'usuario_nombre' => $u->name,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'modelo' => $modelo,
            'modelo_id' => $modeloId,
            'ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
