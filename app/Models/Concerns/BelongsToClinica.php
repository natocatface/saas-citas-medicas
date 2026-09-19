<?php

namespace App\Models\Concerns;

use App\Models\Clinica;
use Illuminate\Database\Eloquent\Builder;

/**
 * Multi-tenant ligero: filtra automáticamente por la clínica del usuario logueado
 * y asigna clinica_id al crear. Superadmin (sin clínica) ve todo.
 */
trait BelongsToClinica
{
    public static function bootBelongsToClinica(): void
    {
        static::creating(function ($model) {
            if (empty($model->clinica_id)) {
                $u = auth()->user();
                if ($u && $u->clinica_id) {
                    $model->clinica_id = $u->clinica_id;
                }
            }
        });

        static::addGlobalScope('clinica', function (Builder $builder) {
            $u = auth()->user();
            if ($u && $u->clinica_id && $u->rol !== 'superadmin') {
                $builder->where($builder->getModel()->getTable().'.clinica_id', $u->clinica_id);
            }
        });
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }
}
