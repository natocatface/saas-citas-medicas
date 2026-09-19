<?php

namespace App\Http\Controllers;

use App\Models\Aseguradora;
use Illuminate\Http\Request;

class AseguradoraController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $aseguradoras = Aseguradora::query()
            ->when($q->isNotEmpty(), fn ($w) => $w->where('nombre', 'like', "%{$q}%")->orWhere('ruc', 'like', "%{$q}%"))
            ->withCount('pacientes')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('aseguradores.index', compact('aseguradoras', 'q'));
    }

    public function create()
    {
        return view('aseguradores.create', ['aseguradora' => new Aseguradora(['activo' => true])]);
    }

    public function store(Request $request)
    {
        Aseguradora::create($this->validar($request));
        return redirect()->route('aseguradores.index')->with('success', 'Aseguradora creada correctamente.');
    }

    public function edit(Aseguradora $aseguradore)
    {
        return view('aseguradores.edit', ['aseguradora' => $aseguradore]);
    }

    public function update(Request $request, Aseguradora $aseguradore)
    {
        $aseguradore->update($this->validar($request));
        return redirect()->route('aseguradores.index')->with('success', 'Aseguradora actualizada correctamente.');
    }

    public function destroy(Aseguradora $aseguradore)
    {
        $aseguradore->delete();
        return redirect()->route('aseguradores.index')->with('success', 'Aseguradora eliminada.');
    }

    private function validar(Request $request): array
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'ruc' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $validated['activo'] = $request->boolean('activo');
        return $validated;
    }
}
