<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use Illuminate\Http\Request;

class ConfiguracionClinicaController extends Controller
{
    public function edit(Request $request)
    {
        $clinica = Clinica::find($request->user()->clinica_id);
        abort_unless($clinica, 404, 'No tienes una clínica asignada.');
        return view('config-clinica.edit', compact('clinica'));
    }

    public function update(Request $request)
    {
        $clinica = Clinica::find($request->user()->clinica_id);
        abort_unless($clinica, 404);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
            'horario' => ['nullable', 'string', 'max:255'],
        ]);
        // 'horario' no es columna; lo guardamos en direccion? No. Solo columnas válidas:
        $clinica->update([
            'nombre' => $data['nombre'],
            'email' => $data['email'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'color' => $data['color'] ?? $clinica->color,
        ]);
        return redirect()->route('config-clinica.edit')->with('success', 'Configuración de la clínica guardada.');
    }
}
