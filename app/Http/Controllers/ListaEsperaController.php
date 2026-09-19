<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\ListaEspera;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListaEsperaController extends Controller
{
    public array $prioridades = ['baja', 'media', 'alta', 'urgente'];
    public array $estados = ['esperando', 'llamado', 'atendido', 'cancelado'];

    public function index(Request $request)
    {
        $estado = $request->string('estado')->trim();
        $registros = ListaEspera::query()
            ->with(['paciente', 'especialidad', 'medico'])
            ->when($estado->isNotEmpty(), fn ($w) => $w->where('estado', (string) $estado))
            ->when($estado->isEmpty(), fn ($w) => $w->where('estado', '!=', 'cancelado'))
            ->orderByRaw("FIELD(prioridad, 'urgente','alta','media','baja')")
            ->orderBy('created_at')
            ->paginate(15)->withQueryString();

        return view('lista-espera.index', [
            'registros' => $registros, 'estadoSel' => (string) $estado,
            'estados' => $this->estados, 'prioridades' => $this->prioridades,
        ]);
    }

    public function create()
    {
        return view('lista-espera.create', $this->formData(new ListaEspera(['prioridad' => 'media', 'estado' => 'esperando'])));
    }

    public function store(Request $request)
    {
        ListaEspera::create($this->validar($request));
        return redirect()->route('lista-espera.index')->with('success', 'Paciente agregado a la lista de espera.');
    }

    public function edit(ListaEspera $registro)
    {
        return view('lista-espera.edit', $this->formData($registro));
    }

    public function update(Request $request, ListaEspera $registro)
    {
        $registro->update($this->validar($request));
        return redirect()->route('lista-espera.index')->with('success', 'Registro actualizado.');
    }

    public function destroy(ListaEspera $registro)
    {
        $registro->delete();
        return redirect()->route('lista-espera.index')->with('success', 'Registro eliminado.');
    }

    public function convertir(ListaEspera $registro)
    {
        if (! $registro->medico_id) {
            return back()->with('error', 'Asigna un médico al registro antes de convertirlo en cita.');
        }
        Cita::create([
            'paciente_id' => $registro->paciente_id,
            'medico_id' => $registro->medico_id,
            'fecha' => Carbon::today()->toDateString(),
            'hora' => Carbon::now()->format('H:i'),
            'estado' => 'confirmada',
            'motivo' => $registro->motivo,
        ]);
        $registro->update(['estado' => 'atendido']);
        return redirect()->route('citas.index')->with('success', 'Cita creada desde la lista de espera.');
    }

    private function formData(ListaEspera $registro): array
    {
        return [
            'registro' => $registro,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::with('especialidad')->orderBy('apellidos')->get(),
            'especialidades' => Especialidad::orderBy('nombre')->get(),
            'prioridades' => $this->prioridades,
            'estados' => $this->estados,
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'medico_id' => ['nullable', 'exists:medicos,id'],
            'prioridad' => ['required', Rule::in($this->prioridades)],
            'estado' => ['required', Rule::in($this->estados)],
            'motivo' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
