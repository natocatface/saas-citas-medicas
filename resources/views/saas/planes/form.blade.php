{{-- Vars: $plan, $action, $method --}}
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $plan->nombre) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio (S/) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="precio" value="{{ old('precio', $plan->precio) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Periodo</label>
                    <select name="periodo" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach(['mensual','trimestral','anual'] as $per)
                            <option value="{{ $per }}" @selected(old('periodo', $plan->periodo) === $per)>{{ ucfirst($per) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Máx. médicos <span class="text-slate-400 text-xs">(vacío = ilimitado)</span></label>
                    <input type="number" min="0" name="max_medicos" value="{{ old('max_medicos', $plan->max_medicos) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Máx. usuarios <span class="text-slate-400 text-xs">(vacío = ilimitado)</span></label>
                    <input type="number" min="0" name="max_usuarios" value="{{ old('max_usuarios', $plan->max_usuarios) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Características <span class="text-slate-400 text-xs">(una por línea)</span></label>
                    <textarea name="caracteristicas" rows="5" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Citas y pacientes&#10;Recetas y facturación&#10;Soporte prioritario">{{ old('caracteristicas', $plan->caracteristicas) }}</textarea>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="destacado" value="1" {{ old('destacado', $plan->destacado) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-slate-700">Plan destacado</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="activo" value="1" {{ old('activo', $plan->activo) ? 'checked' : '' }}
                           class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-slate-700">Plan activo</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('saas.planes.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
