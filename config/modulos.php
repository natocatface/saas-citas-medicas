<?php

// Definicion central del menu lateral.
// 'route' => 'dashboard'            -> ruta dashboard
// 'route' => 'especialidades' ...   -> modulo con CRUD real (usa <route>.index y patron <route>.*)
// 'route' => null                   -> modulo placeholder (pantalla "en construccion")

return [
    'titulos' => [
        'citas'            => 'Citas',
        'lista-espera'     => 'Lista de Espera',
        'calendario'       => 'Calendario',
        'pacientes'        => 'Pacientes',
        'medicos'          => 'Médicos',
        'recetas'          => 'Recetas Médicas',
        'plantillas-cie10' => 'Plantillas (CIE-10)',
        'mensajes'         => 'Mensajes',
        'facturacion'      => 'Facturación',
        'bandeja-sunat'    => 'Comprobantes SUNAT',
        'aseguradores'     => 'Aseguradores',
        'reportes'         => 'Reportes',
        'especialidades'   => 'Especialidades',
        'usuarios'         => 'Usuarios',
        'mantenimiento'    => 'Mantenimiento',
        'plantillas-email' => 'Plantillas de Email',
        'configuracion'    => 'Configuración',
        'facturacion-config' => 'Facturación Electrónica',
    ],

    // En cada item, 'roles' lista los roles que pueden ver el módulo.
    // 'admin' siempre ve todo. Si 'roles' no está definido, lo ven todos.
    'menu' => [
        ['grupo' => null, 'items' => [
            ['key' => 'dashboard',        'titulo' => 'Dashboard',          'icon' => 'home',         'route' => 'dashboard'],
            ['key' => 'citas',            'titulo' => 'Citas',              'icon' => 'calendar',     'route' => 'citas',          'roles' => ['admin','medico','recepcion']],
            ['key' => 'lista-espera',     'titulo' => 'Lista de Espera',    'icon' => 'clock',        'route' => 'lista-espera',   'roles' => ['admin','medico','recepcion']],
            ['key' => 'calendario',       'titulo' => 'Calendario',         'icon' => 'calendar-days','route' => 'calendario',     'roles' => ['admin','medico','recepcion']],
            ['key' => 'pacientes',        'titulo' => 'Pacientes',          'icon' => 'users',        'route' => 'pacientes',      'roles' => ['admin','medico','recepcion']],
            ['key' => 'medicos',          'titulo' => 'Médicos',            'icon' => 'stethoscope',  'route' => 'medicos',        'roles' => ['admin','medico']],
            ['key' => 'recetas',          'titulo' => 'Recetas Méd.',       'icon' => 'document',     'route' => 'recetas',        'roles' => ['admin','medico']],
            ['key' => 'plantillas-cie10', 'titulo' => 'Plantillas (CIE-10)','icon' => 'clipboard',    'route' => 'cie10',          'roles' => ['admin','medico']],
            ['key' => 'mensajes',         'titulo' => 'Mensajes',           'icon' => 'chat',         'route' => 'mensajes'],
            ['key' => 'facturacion',      'titulo' => 'Facturación',        'icon' => 'cash',         'route' => 'facturacion',    'roles' => ['admin','recepcion']],
            ['key' => 'bandeja-sunat',    'titulo' => 'Comprobantes SUNAT', 'icon' => 'list',         'route' => 'bandeja-sunat',  'roles' => ['admin','recepcion']],
            ['key' => 'aseguradores',     'titulo' => 'Aseguradores',       'icon' => 'shield',       'route' => 'aseguradores',   'roles' => ['admin','recepcion']],
            ['key' => 'reportes',         'titulo' => 'Reportes',           'icon' => 'chart',        'route' => 'reportes',       'roles' => ['admin']],
            ['key' => 'especialidades',   'titulo' => 'Especialidades',     'icon' => 'beaker',       'route' => 'especialidades', 'roles' => ['admin','medico']],
        ]],
        ['grupo' => 'ADMINISTRACIÓN', 'items' => [
            ['key' => 'usuarios',         'titulo' => 'Usuarios',           'icon' => 'user-circle',  'route' => 'usuarios',       'roles' => ['admin']],
            ['key' => 'mantenimiento',    'titulo' => 'Mantenimiento',      'icon' => 'wrench',       'route' => 'mantenimiento',  'roles' => ['admin']],
            ['key' => 'plantillas-email', 'titulo' => 'Plantillas de Email','icon' => 'mail',         'route' => 'plantillas-email','roles' => ['admin']],
            ['key' => 'facturacion-config','titulo' => 'Fact. Electrónica', 'icon' => 'receipt',      'route' => 'facturacion-config','roles' => ['admin']],
            ['key' => 'configuracion',    'titulo' => 'Configuración',      'icon' => 'cog',          'route' => 'configuracion',  'roles' => ['admin']],
        ]],
    ],
];
