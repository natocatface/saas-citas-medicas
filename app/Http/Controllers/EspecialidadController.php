<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $especialidades = Especialidad::query()
            ->when($q->isNotEmpty(), fn ($w) => $w->where('nombre', 'like', "%{$q}%"))
            ->withCount('medicos')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('especialidades.index', compact('especialidades', 'q'));
    }

    public function create()
    {
        return view('especialidades.create', ['especialidad' => new Especialidad(['color' => '#6366f1', 'activo' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        Especialidad::create($data);
        return redirect()->route('especialidades.index')->with('success', 'Especialidad creada correctamente.');
    }

    public function edit(Especialidad $especialidade)
    {
        return view('especialidades.edit', ['especialidad' => $especialidade]);
    }

    public function update(Request $request, Especialidad $especialidade)
    {
        $data = $this->validar($request, $especialidade->id);
        $especialidade->update($data);
        return redirect()->route('especialidades.index')->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy(Especialidad $especialidade)
    {
        $especialidade->delete();
        return redirect()->route('especialidades.index')->with('success', 'Especialidad eliminada.');
    }

    private function validar(Request $request, ?int $id = null): array
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:20'],
            'activo' => ['nullable', 'boolean'],
        ], [], [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
        ]);
        $validated['activo'] = $request->boolean('activo');
        return $validated;
    }
}
