<?php

namespace App\Http\Controllers;

use App\Models\PlantillaEmail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlantillaEmailController extends Controller
{
    public array $tipos = ['general', 'recordatorio', 'bienvenida', 'confirmacion'];

    public function index()
    {
        $plantillas = PlantillaEmail::orderBy('nombre')->paginate(12);
        return view('plantillas-email.index', compact('plantillas'));
    }

    public function create()
    {
        return view('plantillas-email.create', ['plantilla' => new PlantillaEmail(['activo' => true, 'tipo' => 'general']), 'tipos' => $this->tipos]);
    }

    public function store(Request $request)
    {
        PlantillaEmail::create($this->validar($request));
        return redirect()->route('plantillas-email.index')->with('success', 'Plantilla creada.');
    }

    public function edit(PlantillaEmail $plantillas_email)
    {
        return view('plantillas-email.edit', ['plantilla' => $plantillas_email, 'tipos' => $this->tipos]);
    }

    public function update(Request $request, PlantillaEmail $plantillas_email)
    {
        $plantillas_email->update($this->validar($request));
        return redirect()->route('plantillas-email.index')->with('success', 'Plantilla actualizada.');
    }

    public function destroy(PlantillaEmail $plantillas_email)
    {
        $plantillas_email->delete();
        return redirect()->route('plantillas-email.index')->with('success', 'Plantilla eliminada.');
    }

    private function validar(Request $request): array
    {
        $v = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'tipo' => ['required', Rule::in($this->tipos)],
            'asunto' => ['required', 'string', 'max:200'],
            'cuerpo' => ['required', 'string', 'max:5000'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $v['activo'] = $request->boolean('activo');
        return $v;
    }
}
