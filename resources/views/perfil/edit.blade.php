@extends('layouts.app')

@section('title', 'Mi perfil')
@section('subtitle', 'Datos de tu cuenta')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Tarjeta resumen --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-white text-lg font-bold flex items-center justify-center">
            {{ $usuario->iniciales() }}
        </div>
        <div>
            <p class="text-lg font-bold text-slate-800">{{ $usuario->name }}</p>
            <p class="text-sm text-slate-500">{{ $usuario->email }}</p>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-violet-100 text-violet-700">{{ ucfirst($usuario->rol) }}</span>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('perfil.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre completo <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo electrónico <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-4">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Cambiar contraseña</p>
                    <p class="text-xs text-slate-400">Déjala en blanco para mantener la actual.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nueva contraseña</label>
                        <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar cambios</button>
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Volver</a>
            </div>
        </form>
    </div>
</div>
@endsection
