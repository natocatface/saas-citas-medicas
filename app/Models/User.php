<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'telefono',
        'activo',
        'clinica_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }

    public function esSuperadmin(): bool
    {
        return $this->rol === 'superadmin';
    }

    public function iniciales(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = mb_substr($parts[1] ?? '', 0, 1);
        return mb_strtoupper($a.$b);
    }
}
