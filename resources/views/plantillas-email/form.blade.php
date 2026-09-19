{{-- Vars: $plantilla, $tipos, $action, $method --}}
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $plantilla->nombre) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipo</label>
                    <select name="tipo" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($tipos as $t)
                            <option value="{{ $t }}" @selected(old('tipo', $plantilla->tipo) === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Asunto <span class="text-rose-500">*</span></label>
                    <input type="text" name="asunto" value="{{ old('asunto', $plantilla->asunto) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Cuerpo <span class="text-rose-500">*</span></label>
                    <textarea name="cuerpo" rows="8" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('cuerpo', $plantilla->cuerpo) }}</textarea>
                    <p class="text-xs text-slate-400 mt-1.5">Variables disponibles: <span class="font-mono text-slate-600">&#123;paciente&#125;</span>, <span class="font-mono text-slate-600">&#123;medico&#125;</span>, <span class="font-mono text-slate-600">&#123;fecha&#125;</span>, <span class="font-mono text-slate-600">&#123;hora&#125;</span>, <span class="font-mono text-slate-600">&#123;clinica&#125;</span></p>
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ old('activo', $plantilla->activo) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">Plantilla activa</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('plantillas-email.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
