<?php

namespace App\Support;

class Menu
{
    /**
     * Devuelve los grupos del menú visibles para un rol, con URL y estado activo
     * ya calculados. Así la vista no necesita lógica (ni closures en @php).
     *
     * @return array<int, array{label: ?string, items: array<int, array{titulo:string, icon:string, url:string, active:bool}>}>
     */
    public static function paraRol(string $rol): array
    {
        if ($rol === 'superadmin') {
            return self::menuSuperadmin();
        }

        $grupos = [];

        foreach ((array) config('modulos.menu', []) as $grupo) {
            $items = [];

            foreach ($grupo['items'] ?? [] as $it) {
                $roles = $it['roles'] ?? null;
                $puede = $rol === 'admin' || empty($roles) || in_array($rol, $roles, true);
                if (! $puede) {
                    continue;
                }

                $ruta = $it['route'] ?? null;
                if ($ruta === 'dashboard') {
                    $url = route('dashboard');
                    $active = request()->routeIs('dashboard');
                } elseif ($ruta) {
                    $url = route($ruta.'.index');
                    $active = request()->routeIs($ruta.'.*');
                } else {
                    $url = route('modulo', $it['key']);
                    $active = request()->routeIs('modulo') && request()->route('modulo') === $it['key'];
                }

                $items[] = [
                    'titulo' => $it['titulo'],
                    'icon' => $it['icon'],
                    'url' => $url,
                    'active' => $active,
                ];
            }

            if (! empty($items)) {
                $grupos[] = ['label' => $grupo['grupo'] ?? null, 'items' => $items];
            }
        }

        return $grupos;
    }

    /** Menú exclusivo del Super Admin (plataforma SaaS). */
    private static function menuSuperadmin(): array
    {
        $items = [
            ['Panel SaaS',     'home',        'saas.dashboard',         'saas.dashboard'],
            ['Clínicas',       'building',    'saas.clinicas.index',    'saas.clinicas.*'],
            ['Planes',         'ticket',      'saas.planes.index',      'saas.planes.*'],
            ['Usuarios',       'user-circle', 'saas.usuarios.index',    'saas.usuarios.*'],
            ['Bitácora',       'list',        'saas.bitacora.index',    'saas.bitacora.*'],
            ['Configuración',  'cog',         'saas.configuracion.edit','saas.configuracion.*'],
        ];

        $render = [];
        foreach ($items as [$titulo, $icon, $ruta, $patron]) {
            $render[] = [
                'titulo' => $titulo,
                'icon' => $icon,
                'url' => route($ruta),
                'active' => request()->routeIs($patron),
            ];
        }

        return [['label' => 'PLATAFORMA SaaS', 'items' => $render]];
    }
}
