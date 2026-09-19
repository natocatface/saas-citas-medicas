<?php

return [
    // País por defecto del tenant (puede sobreescribirse por clínica).
    'pais_defecto' => env('FACT_PAIS', 'PE'),

    // Disco de Laravel donde se almacenan XML firmados, CDR y PDF.
    'disco' => env('FACT_DISCO', 'local'),

    // Series por defecto (Perú).
    'series' => [
        'factura' => env('FACT_SERIE_FACTURA', 'F001'),
        'boleta' => env('FACT_SERIE_BOLETA', 'B001'),
        'nota_credito' => env('FACT_SERIE_NC', 'FC01'),
    ],

    // Datos del emisor por defecto (fallback si no hay config por clínica).
    'emisor' => [
        'ruc' => env('FACT_RUC', '20000000001'),
        'razon_social' => env('FACT_RAZON_SOCIAL', 'CLINICA DEMO S.A.C.'),
    ],

    // Configuración por país. 'habilitado' => false mantiene el modo simulado.
    'proveedores' => [
        'PE' => [
            'habilitado' => env('FACT_PE_HABILITADO', false),
            'modo' => env('FACT_PE_MODO', 'beta'), // beta (homologación) | produccion
            'endpoint' => env('FACT_PE_ENDPOINT', 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService'),
            'usuario_sol' => env('FACT_PE_SOL_USER'),
            'clave_sol' => env('FACT_PE_SOL_PASS'),
            'certificado' => env('FACT_PE_CERT_PATH'), // ruta al .pem (llave + certificado)
        ],
        'CO' => ['habilitado' => false],
        'CL' => ['habilitado' => false],
        'AR' => ['habilitado' => false],
        'MX' => ['habilitado' => false],
    ],

    // Cola usada para emisión asíncrona / reintentos.
    'cola' => env('FACT_COLA', 'facturacion'),
];
