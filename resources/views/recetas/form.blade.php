{{-- Vars: $receta, $pacientes, $medicos, $items, $action, $method --}}
@php
    $itemsInit = collect($items)->map(fn ($it) => [
        'medicamento' => $it['medicamento'] ?? $it->medicamento ?? '',
        'dosis' => $it['dosis'] ?? $it->dosis ?? '',
        'frecuencia' => $it['frecuencia'] ?? $it->frecuencia ?? '',
        'duracion' => $it['duracion'] ?? $it->duracion ?? '',
        'indicaciones' => $it['indicaciones'] ?? $it->indicaciones ?? '',
    ])->values();
@endphp

<div class="max-w-4xl">
    <form method="POST" action="{{ $action }}"
          x-data="{
              items: {{ Illuminate\Support\Js::from($itemsInit) }},
              add() { this.items.push({medicamento:'', dosis:'', frecuencia:'', duracion:'', indicaciones:''}); },
              remove(i) { this.items.splice(i,1); if(!this.items.length) this.add(); }
          }"
          x-init="if(!items.length) add()"
          class="space-y-5">
        @csrf
        @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
                    <select name="paciente_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Seleccionar —</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id', $receta->paciente_id) == $p->id)>{{ $p->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Médico <span class="text-rose-500">*</span></label>
                    <select name="medico_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Seleccionar —</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}" @selected(old('medico_id', $receta->medico_id) == $m->id)>{{ $m->nombre_completo }} {{ $m->especialidad ? '· '.$m->especialidad->nombre : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', optional($receta->fecha)->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Diagnóstico</label>
                    <input type="text" name="diagnostico" value="{{ old('diagnostico', $receta->diagnostico) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>
        </div>

        {{-- Medicamentos --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Medicamentos</h3>
                <button type="button" @click="add()" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Agregar medicamento
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(it, i) in items" :key="i">
                    <div class="rounded-xl border border-slate-200 p-3 grid grid-cols-1 sm:grid-cols-12 gap-2 items-start">
                        <input type="text" x-model="it.medicamento" :name="`items[${i}][medicamento]`" placeholder="Medicamento"
                               class="sm:col-span-4 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <input type="text" x-model="it.dosis" :name="`items[${i}][dosis]`" placeholder="Dosis"
                               class="sm:col-span-2 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <input type="text" x-model="it.frecuencia" :name="`items[${i}][frecuencia]`" placeholder="Frecuencia"
                               class="sm:col-span-2 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <input type="text" x-model="it.duracion" :name="`items[${i}][duracion]`" placeholder="Duración"
                               class="sm:col-span-2 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <div class="sm:col-span-2 flex items-center gap-1">
                            <input type="text" x-model="it.indicaciones" :name="`items[${i}][indicaciones]`" placeholder="Indicación"
                                   class="flex-1 rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                            <button type="button" @click="remove(i)" class="text-slate-400 hover:text-rose-500 shrink-0" title="Quitar">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Indicaciones generales</label>
                <textarea name="indicaciones" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('indicaciones', $receta->indicaciones) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
                <textarea name="notas" rows="3" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notas', $receta->notas) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar receta</button>
            <a href="{{ route('recetas.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
        </div>
    </form>
</div>
