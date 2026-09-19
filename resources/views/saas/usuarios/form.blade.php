{{-- Vars: $usuario, $roles, $clinicas, $action, $method --}}
@php($esEdicion = ($method ?? 'POST') === 'PUT')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ $action }}" class="space-y-5"
              x-data="{ rol: '{{ old('rol', $usuario->rol) }}' }">
            @csrf
            @if($esEdicion) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre completo <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Rol <span class="text-rose-500">*</span></label>
                    <select name="rol" x-model="rol" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($roles as $k => $label)
                            <option value="{{ $k }}" @selected(old('rol', $usuario->rol) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="rol !== 'superadmin'" class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Clínica</label>
                    <select name="clinica_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Sin clínica —</option>
                        @foreach($clinicas as $cl)
                            <option value="{{ $cl->id }}" @selected(old('clinica_id', $usuario->clinica_id) == $cl->id)>{{ $cl->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">El Super Admin no pertenece a ninguna clínica.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-4">
                <p class="text-sm font-semibold text-slate-700">{{ $esEdicion ? 'Cambiar contraseña' : 'Contraseña' }}</p>
                @if($esEdicion)<p class="text-xs text-slate-400 -mt-2">Déjala en blanco para mantener la actual.</p>@endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Contraseña {!! $esEdicion ? '' : '<span class="text-rose-500">*</span>' !!}</label>
                        <input type="password" name="password" {{ $esEdicion ? '' : 'required' }}
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar</label>
                        <input type="password" name="password_confirmation"
                               class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm text-slate-700">Cuenta activa</span>
            </label>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar</button>
                <a href="{{ route('saas.usuarios.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
