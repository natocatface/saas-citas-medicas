<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClinicaController extends Controller
{
    public array $estados = ['prueba', 'activa', 'suspendida', 'cancelada'];

    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $estado = $request->string('estado')->trim();

        $clinicas = Clinica::query()
            ->with('plan')
            ->withCount(['usuarios', 'medicos', 'pacientes'])
            ->when($q->isNotEmpty(), fn ($w) => $w->where('nombre', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->when($estado->isNotEmpty(), fn ($w) => $w->where('estado_suscripcion', (string) $estado))
            ->orderBy('nombre')
            ->paginate(12)->withQueryString();

        return view('saas.clinicas.index', [
            'clinicas' => $clinicas, 'q' => $q, 'estadoSel' => (string) $estado, 'estados' => $this->estados,
        ]);
    }

    public function create()
    {
        return view('saas.clinicas.create', [
            'clinica' => new Clinica(['estado_suscripcion' => 'prueba', 'activo' => true, 'color' => '#17b8cf']),
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
            'estados' => $this->estados,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['slug'] = $this->slugUnico($data['nombre']);
        Clinica::create($data);
        return redirect()->route('saas.clinicas.index')->with('success', 'Clínica creada correctamente.');
    }

    public function edit(Clinica $clinica)
    {
        return view('saas.clinicas.edit', [
            'clinica' => $clinica,
            'planes' => Plan::where('activo', true)->orderBy('precio')->get(),
            'estados' => $this->estados,
        ]);
    }

    public function update(Request $request, Clinica $clinica)
    {
        $clinica->update($this->validar($request));
        return redirect()->route('saas.clinicas.index')->with('success', 'Clínica actualizada correctamente.');
    }

    public function destroy(Clinica $clinica)
    {
        $clinica->delete();
        return redirect()->route('saas.clinicas.index')->with('success', 'Clínica eliminada.');
    }

    private function slugUnico(string $nombre): string
    {
        $base = Str::slug($nombre);
        $slug = $base;
        $i = 1;
        while (Clinica::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }
        return $slug;
    }

    private function validar(Request $request): array
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'estado_suscripcion' => ['required', Rule::in($this->estados)],
            'suscripcion_vence' => ['nullable', 'date'],
            'color' => ['nullable', 'string', 'max:20'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $validated['activo'] = $request->boolean('activo');
        return $validated;
    }
}
