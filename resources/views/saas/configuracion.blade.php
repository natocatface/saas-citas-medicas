@extends('layouts.app')

@section('title', 'Configuración')
@section('subtitle', 'Parámetros generales de la plataforma')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('saas.configuracion.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre de la plataforma</label>
                    <input type="text" name="nombre_plataforma" value="{{ old('nombre_plataforma', $config['nombre_plataforma'] ?? 'CitasMédicas') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo de soporte</label>
                    <input type="email" name="correo_soporte" value="{{ old('correo_soporte', $config['correo_soporte'] ?? '') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Teléfono de soporte</label>
                    <input type="text" name="telefono_soporte" value="{{ old('telefono_soporte', $config['telefono_soporte'] ?? '') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Moneda</label>
                    <input type="text" name="moneda" value="{{ old('moneda', $config['moneda'] ?? 'S/') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Días de prueba</label>
                    <input type="number" min="0" max="365" name="dias_prueba" value="{{ old('dias_prueba', $config['dias_prueba'] ?? '30') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pie de página en facturas</label>
                    <input type="text" name="pie_factura" value="{{ old('pie_factura', $config['pie_factura'] ?? '') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Gracias por su preferencia">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Mensaje en pantalla de login</label>
                    <input type="text" name="mensaje_login" value="{{ old('mensaje_login', $config['mensaje_login'] ?? '') }}"
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar configuración</button>
            </div>
        </form>
    </div>
</div>
@endsection
