<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $especialidadId = $request->integer('especialidad_id');

        $medicos = Medico::query()
            ->with('especialidad')
            ->when($q->isNotEmpty(), function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%")
                      ->orWhere('documento', 'like', "%{$q}%");
                });
            })
            ->when($especialidadId, fn ($w) => $w->where('especialidad_id', $especialidadId))
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('medicos.index', compact('medicos', 'especialidades', 'q', 'especialidadId'));
    }

    public function create()
    {
        return view('medicos.create', [
            'medico' => new Medico(['activo' => true]),
            'especialidades' => Especialidad::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Medico::create($this->validar($request));
        return redirect()->route('medicos.index')->with('success', 'Médico registrado correctamente.');
    }

    public function edit(Medico $medico)
    {
        return view('medicos.edit', [
            'medico' => $medico,
            'especialidades' => Especialidad::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Medico $medico)
    {
        $medico->update($this->validar($request));
        return redirect()->route('medicos.index')->with('success', 'Médico actualizado correctamente.');
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();
        return redirect()->route('medicos.index')->with('success', 'Médico eliminado.');
    }

    private function validar(Request $request): array
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'documento' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'numero_colegiatura' => ['nullable', 'string', 'max:50'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $validated['activo'] = $request->boolean('activo');
        return $validated;
    }
}
