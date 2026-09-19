<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planes = Plan::withCount('clinicas')->orderBy('precio')->paginate(12);
        return view('saas.planes.index', compact('planes'));
    }

    public function create()
    {
        return view('saas.planes.create', ['plan' => new Plan(['activo' => true, 'periodo' => 'mensual'])]);
    }

    public function store(Request $request)
    {
        Plan::create($this->validar($request));
        return redirect()->route('saas.planes.index')->with('success', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan)
    {
        return view('saas.planes.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validar($request));
        return redirect()->route('saas.planes.index')->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('saas.planes.index')->with('success', 'Plan eliminado.');
    }

    private function validar(Request $request): array
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'precio' => ['required', 'numeric', 'min:0'],
            'periodo' => ['required', 'string', 'max:20'],
            'max_medicos' => ['nullable', 'integer', 'min:0'],
            'max_usuarios' => ['nullable', 'integer', 'min:0'],
            'caracteristicas' => ['nullable', 'string', 'max:2000'],
            'destacado' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $validated['destacado'] = $request->boolean('destacado');
        $validated['activo'] = $request->boolean('activo');
        return $validated;
    }
}
