{{-- Vars: $item, $action, $method --}}
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Código <span class="text-rose-500">*</span></label>
                    <input type="text" name="codigo" value="{{ old('codigo', $item->codigo) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej. J00">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoría</label>
                    <input type="text" name="categoria" value="{{ old('categoria', $item->categoria) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej. Enfermedades respiratorias">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripción <span class="text-rose-500">*</span></label>
                    <input type="text" name="descripcion" value="{{ old('descripcion', $item->descripcion) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej. Rinofaringitis aguda (resfriado común)">
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ old('activo', $item->activo) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">Activo</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('cie10.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
