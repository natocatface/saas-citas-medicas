@extends('layouts.app')

@section('title', 'Configuración')
@section('subtitle', 'Datos y parámetros de tu clínica')

@section('content')
<div class="max-w-2xl space-y-5">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white" style="background: {{ $clinica->color }}">
            @include('partials.icon', ['name' => 'building', 'class' => 'w-7 h-7'])
        </div>
        <div>
            <p class="text-lg font-bold text-slate-800">{{ $clinica->nombre }}</p>
            <p class="text-sm text-slate-500">Plan {{ $clinica->plan->nombre ?? '—' }} · {{ ucfirst($clinica->estado_suscripcion) }}</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('configuracion.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

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
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Horario de atención</label>
                    <input type="text" name="horario" value="{{ old('horario') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Lun a Vie 08:00–18:00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Color de la clínica</label>
                    <input type="color" name="color" value="{{ old('color', $clinica->color ?: '#17b8cf') }}"
                           class="h-10 w-14 rounded-lg border border-slate-300 cursor-pointer p-1">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar configuración</button>
            </div>
        </form>
    </div>
</div>
@endsection
