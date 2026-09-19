<?php

namespace App\Http\Controllers;

use App\Models\Aseguradora;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();

        $pacientes = Paciente::query()
            ->with('aseguradora')
            ->when($q->isNotEmpty(), function ($w) use ($q) {
                $w->where(function ($s) use ($q) {
                    $s->where('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%")
                      ->orWhere('documento', 'like', "%{$q}%")
                      ->orWhere('telefono', 'like', "%{$q}%");
                });
            })
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('pacientes.index', compact('pacientes', 'q'));
    }

    public function create()
    {
        return view('pacientes.create', [
            'paciente' => new Paciente(['activo' => true]),
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Paciente::create($this->validar($request));
        return redirect()->route('pacientes.index')->with('success', 'Paciente registrado correctamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', [
            'paciente' => $paciente,
            'aseguradoras' => Aseguradora::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Paciente $paciente)
    {
        $paciente->update($this->validar($request));
        return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return redirect()->route('pacientes.index')->with('success', 'Paciente eliminado.');
    }

    private function validar(Request $request): array
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:120'],
            'apellidos' => ['required', 'string', 'max:120'],
            'documento' => ['nullable', 'string', 'max:30'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'sexo' => ['nullable', 'in:M,F,O'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'aseguradora_id' => ['nullable', 'exists:aseguradoras,id'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $validated['activo'] = $request->boolean('activo');
        return $validated;
    }
}
