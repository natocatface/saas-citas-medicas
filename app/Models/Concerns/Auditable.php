<?php

namespace App\Models\Concerns;

use App\Models\Bitacora;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($m) {
            Bitacora::registrar('Creó '.class_basename($m), (string) $m->getKey(), class_basename($m));
        });
        static::updated(function ($m) {
            Bitacora::registrar('Actualizó '.class_basename($m), (string) $m->getKey(), class_basename($m));
        });
        static::deleted(function ($m) {
            Bitacora::registrar('Eliminó '.class_basename($m), (string) $m->getKey(), class_basename($m));
        });
    }
}
