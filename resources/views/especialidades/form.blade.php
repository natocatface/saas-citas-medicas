{{-- Form compartido. Vars: $especialidad, $action, $method --}}
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT')
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $especialidad->nombre) }}" required
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej. Cardiología">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripción</label>
                <input type="text" name="descripcion" value="{{ old('descripcion', $especialidad->descripcion) }}"
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Breve descripción">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Color identificador</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" value="{{ old('color', $especialidad->color ?: '#6366f1') }}"
                           class="h-10 w-14 rounded-lg border border-slate-300 cursor-pointer p-1">
                    <span class="text-xs text-slate-400">Se usa en el calendario y reportes.</span>
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ old('activo', $especialidad->activo) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">Especialidad activa</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('especialidades.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
