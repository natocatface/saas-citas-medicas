{{-- Vars: $clinica, $planes, $estados, $action, $method --}}
<div class="max-w-3xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5">
            @csrf
            @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre de la clínica <span class="text-rose-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $clinica->nombre) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo</label>
                    <input type="email" name="email" value="{{ old('email', $clinica->email) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $clinica->telefono) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Dirección</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $clinica->direccion) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Plan</label>
                    <select name="plan_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Sin plan —</option>
                        @foreach($planes as $pl)
                            <option value="{{ $pl->id }}" @selected(old('plan_id', $clinica->plan_id) == $pl->id)>{{ $pl->nombre }} (S/ {{ number_format($pl->precio, 0) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado de suscripción</label>
                    <select name="estado_suscripcion" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($estados as $e)
                            <option value="{{ $e }}" @selected(old('estado_suscripcion', $clinica->estado_suscripcion) === $e)>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Vence el</label>
                    <input type="date" name="suscripcion_vence" value="{{ old('suscripcion_vence', optional($clinica->suscripcion_vence)->format('Y-m-d')) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Color</label>
                    <input type="color" name="color" value="{{ old('color', $clinica->color ?: '#17b8cf') }}"
                           class="h-10 w-14 rounded-lg border border-slate-300 cursor-pointer p-1">
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ old('activo', $clinica->activo) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">Clínica activa</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('saas.clinicas.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
