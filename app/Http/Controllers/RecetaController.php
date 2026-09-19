<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $recetas = Receta::query()
            ->with(['paciente', 'medico'])
            ->when($q->isNotEmpty(), function ($w) use ($q) {
                $w->where('numero', 'like', "%{$q}%")
                  ->orWhereHas('paciente', fn ($s) => $s->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%"));
            })
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(12)->withQueryString();

        return view('recetas.index', compact('recetas', 'q'));
    }

    public function create()
    {
        return view('recetas.create', [
            'receta' => new Receta(['fecha' => now()->toDateString()]),
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::with('especialidad')->orderBy('apellidos')->get(),
            'items' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        DB::transaction(function () use ($data, $request) {
            $numero = 'REC-'.str_pad((string) ((Receta::max('id') ?? 0) + 1), 5, '0', STR_PAD_LEFT);
            $receta = Receta::create(array_merge($data, ['numero' => $numero]));
            $this->guardarItems($receta, $request->input('items', []));
        });
        return redirect()->route('recetas.index')->with('success', 'Receta creada correctamente.');
    }

    public function show(Receta $receta)
    {
        $receta->load(['paciente', 'medico.especialidad', 'items']);
        return view('recetas.show', compact('receta'));
    }

    public function edit(Receta $receta)
    {
        $receta->load('items');
        return view('recetas.edit', [
            'receta' => $receta,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::with('especialidad')->orderBy('apellidos')->get(),
            'items' => $receta->items,
        ]);
    }

    public function update(Request $request, Receta $receta)
    {
        $data = $this->validar($request);
        DB::transaction(function () use ($data, $request, $receta) {
            $receta->update($data);
            $receta->items()->delete();
            $this->guardarItems($receta, $request->input('items', []));
        });
        return redirect()->route('recetas.index')->with('success', 'Receta actualizada correctamente.');
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();
        return redirect()->route('recetas.index')->with('success', 'Receta eliminada.');
    }

    private function guardarItems(Receta $receta, array $items): void
    {
        foreach ($items as $row) {
            $med = trim((string) ($row['medicamento'] ?? ''));
            if ($med === '') {
                continue;
            }
            $receta->items()->create([
                'medicamento' => $med,
                'dosis' => $row['dosis'] ?? null,
                'frecuencia' => $row['frecuencia'] ?? null,
                'duracion' => $row['duracion'] ?? null,
                'indicaciones' => $row['indicaciones'] ?? null,
            ]);
        }
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['required', 'exists:medicos,id'],
            'cita_id' => ['nullable', 'exists:citas,id'],
            'fecha' => ['required', 'date'],
            'diagnostico' => ['nullable', 'string', 'max:255'],
            'indicaciones' => ['nullable', 'string', 'max:2000'],
            'notas' => ['nullable', 'string', 'max:2000'],
            'items' => ['array'],
        ]);
    }
}
