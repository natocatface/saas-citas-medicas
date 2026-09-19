<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitaController extends Controller
{
    public array $estados = ['pendiente', 'confirmada', 'atendida', 'cancelada'];

    public function index(Request $request)
    {
        $q = $request->string('q')->trim();
        $estado = $request->string('estado')->trim();
        $fecha = $request->date('fecha');

        $citas = Cita::query()
            ->with(['paciente', 'medico.especialidad'])
            ->when($q->isNotEmpty(), function ($w) use ($q) {
                $w->whereHas('paciente', function ($s) use ($q) {
                    $s->where('nombres', 'like', "%{$q}%")
                      ->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->when($estado->isNotEmpty(), fn ($w) => $w->where('estado', (string) $estado))
            ->when($fecha, fn ($w) => $w->whereDate('fecha', $fecha))
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate(12)
            ->withQueryString();

        return view('citas.index', [
            'citas' => $citas,
            'q' => $q,
            'estadoSel' => (string) $estado,
            'fecha' => $request->input('fecha'),
            'estados' => $this->estados,
        ]);
    }

    public function create()
    {
        return view('citas.create', [
            'cita' => new Cita(['estado' => 'pendiente', 'fecha' => now()->toDateString()]),
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::with('especialidad')->orderBy('apellidos')->get(),
            'estados' => $this->estados,
        ]);
    }

    public function store(Request $request)
    {
        Cita::create($this->validar($request));
        return redirect()->route('citas.index')->with('success', 'Cita agendada correctamente.');
    }

    public function show(Cita $cita)
    {
        $cita->load(['paciente.aseguradora', 'medico.especialidad']);
        return view('citas.show', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        return view('citas.edit', [
            'cita' => $cita,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::with('especialidad')->orderBy('apellidos')->get(),
            'estados' => $this->estados,
        ]);
    }

    public function update(Request $request, Cita $cita)
    {
        $cita->update($this->validar($request));
        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();
        return redirect()->route('citas.index')->with('success', 'Cita eliminada.');
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in($this->estados)],
        ]);
        $cita->update($data);
        return back()->with('success', 'Estado de la cita actualizado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['required', 'exists:medicos,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'estado' => ['required', Rule::in($this->estados)],
            'motivo' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
