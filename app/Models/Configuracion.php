<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = ['clave', 'valor'];

    public static function get(string $clave, ?string $default = null): ?string
    {
        $all = Cache::rememberForever('configuraciones', fn () => static::pluck('valor', 'clave')->all());
        return $all[$clave] ?? $default;
    }

    public static function set(string $clave, ?string $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget('configuraciones');
    }
}
