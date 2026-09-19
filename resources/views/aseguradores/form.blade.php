{{-- Vars: $aseguradora, $action, $method --}}
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $aseguradora->nombre) }}" required
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Ej. Seguros Salud Total">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">RUC / NIT</label>
                    <input type="text" name="ruc" value="{{ old('ruc', $aseguradora->ruc) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $aseguradora->telefono) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email', $aseguradora->email) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ old('activo', $aseguradora->activo) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">Aseguradora activa</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('aseguradores.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
