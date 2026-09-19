{{-- Vars: $registro, $pacientes, $medicos, $especialidades, $prioridades, $estados, $action, $method --}}
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
                <select name="paciente_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">— Seleccionar —</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id', $registro->paciente_id) == $p->id)>{{ $p->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Especialidad</label>
                    <select name="especialidad_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Cualquiera —</option>
                        @foreach($especialidades as $e)
                            <option value="{{ $e->id }}" @selected(old('especialidad_id', $registro->especialidad_id) == $e->id)>{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Médico (para convertir en cita)</label>
                    <select name="medico_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Sin asignar —</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}" @selected(old('medico_id', $registro->medico_id) == $m->id)>{{ $m->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Prioridad <span class="text-rose-500">*</span></label>
                    <select name="prioridad" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($prioridades as $p)
                            <option value="{{ $p }}" @selected(old('prioridad', $registro->prioridad) === $p)>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado <span class="text-rose-500">*</span></label>
                    <select name="estado" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($estados as $e)
                            <option value="{{ $e }}" @selected(old('estado', $registro->estado) === $e)>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Motivo</label>
                    <input type="text" name="motivo" value="{{ old('motivo', $registro->motivo) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
                    <textarea name="notas" rows="2" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notas', $registro->notas) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('lista-espera.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
