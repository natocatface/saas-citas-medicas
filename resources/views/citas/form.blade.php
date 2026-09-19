{{-- Vars: $cita, $pacientes, $medicos, $estados, $action, $method --}}
<div class="max-w-3xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
                    <select name="paciente_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Seleccionar paciente —</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id', $cita->paciente_id) == $p->id)>{{ $p->nombre_completo }} {{ $p->documento ? '· '.$p->documento : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Médico <span class="text-rose-500">*</span></label>
                    <select name="medico_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Seleccionar médico —</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}" @selected(old('medico_id', $cita->medico_id) == $m->id)>{{ $m->nombre_completo }} {{ $m->especialidad ? '· '.$m->especialidad->nombre : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', optional($cita->fecha)->format('Y-m-d') ?: $cita->fecha) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora <span class="text-rose-500">*</span></label>
                    <input type="time" name="hora" value="{{ old('hora', \Illuminate\Support\Str::of($cita->hora)->substr(0,5)) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado <span class="text-rose-500">*</span></label>
                    <select name="estado" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($estados as $e)
                            <option value="{{ $e }}" @selected(old('estado', $cita->estado) === $e)>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Motivo</label>
                    <input type="text" name="motivo" value="{{ old('motivo', $cita->motivo) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej. Control, dolor abdominal...">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
                    <textarea name="notas" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Observaciones internas...">{{ old('notas', $cita->notas) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('citas.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
