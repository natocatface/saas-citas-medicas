<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    private array $campos = [
        'nombre_plataforma', 'correo_soporte', 'telefono_soporte',
        'moneda', 'dias_prueba', 'pie_factura', 'mensaje_login',
    ];

    public function edit()
    {
        $config = [];
        foreach ($this->campos as $c) {
            $config[$c] = Configuracion::get($c);
        }
        return view('saas.configuracion', ['config' => $config]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nombre_plataforma' => ['nullable', 'string', 'max:120'],
            'correo_soporte' => ['nullable', 'email', 'max:150'],
            'telefono_soporte' => ['nullable', 'string', 'max:40'],
            'moneda' => ['nullable', 'string', 'max:10'],
            'dias_prueba' => ['nullable', 'integer', 'min:0', 'max:365'],
            'pie_factura' => ['nullable', 'string', 'max:255'],
            'mensaje_login' => ['nullable', 'string', 'max:255'],
        ]);
        foreach ($this->campos as $c) {
            Configuracion::set($c, $data[$c] ?? null);
        }
        return redirect()->route('saas.configuracion.edit')->with('success', 'Configuración guardada.');
    }
}
