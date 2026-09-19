@extends('layouts.app')

@section('title', 'Mantenimiento')
@section('subtitle', 'Estado del sistema y herramientas')

@section('content')
<div class="space-y-6">

    {{-- Información del sistema --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                @include('partials.icon', ['name' => 'cog', 'class' => 'w-5 h-5'])
                Información del sistema
            </h3>
            <dl class="divide-y divide-slate-100">
                @foreach($info as $k => $v)
                    <div class="flex items-center justify-between py-2.5">
                        <dt class="text-sm text-slate-500">{{ $k }}</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $v }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Conteo de registros --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-slate-800 mb-4">Registros de tu clínica</h3>
            <div class="space-y-3">
                @foreach($conteos as $c)
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center text-white shrink-0" style="background: {{ $c[3] }}">
                            @include('partials.icon', ['name' => $c[2], 'class' => 'w-5 h-5'])
                        </span>
                        <span class="text-sm text-slate-600 flex-1">{{ $c[0] }}</span>
                        <span class="text-lg font-bold text-slate-800">{{ number_format($c[1]) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Herramientas --}}
    <div>
        <h3 class="font-bold text-slate-800 mb-3">Herramientas de mantenimiento</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Limpiar caché --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 flex flex-col">
                <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    @include('partials.icon', ['name' => 'refresh', 'class' => 'w-5 h-5'])
                </div>
                <h4 class="mt-4 font-bold text-slate-800">Limpiar caché</h4>
                <p class="mt-1 text-sm text-slate-500 flex-1">Limpia vistas compiladas, rutas y configuración. Útil si no ves los últimos cambios.</p>
                <form method="POST" action="{{ route('mantenimiento.cache') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg py-2.5 shadow-sm">Limpiar caché</button>
                </form>
            </div>

            {{-- Respaldo --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 flex flex-col">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    @include('partials.icon', ['name' => 'document', 'class' => 'w-5 h-5'])
                </div>
                <h4 class="mt-4 font-bold text-slate-800">Respaldo de datos</h4>
                <p class="mt-1 text-sm text-slate-500 flex-1">Descarga un archivo JSON con pacientes, médicos, citas, facturas y recetas de tu clínica.</p>
                <a href="{{ route('mantenimiento.respaldo') }}" class="mt-4 text-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg py-2.5 shadow-sm">Descargar respaldo</a>
            </div>

            {{-- Limpiar bitácora --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 flex flex-col" x-data="{ confirm: false }">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    @include('partials.icon', ['name' => 'list', 'class' => 'w-5 h-5'])
                </div>
                <h4 class="mt-4 font-bold text-slate-800">Limpiar bitácora</h4>
                <p class="mt-1 text-sm text-slate-500 flex-1">Elimina registros de auditoría con más de 90 días. Hay <span class="font-semibold text-slate-700">{{ number_format($bitacoraAntigua) }}</span> registro(s) antiguos.</p>
                <button type="button" @click="confirm = true" {{ $bitacoraAntigua === 0 ? 'disabled' : '' }}
                        class="mt-4 w-full bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg py-2.5 shadow-sm {{ $bitacoraAntigua === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Limpiar antiguos
                </button>

                <template x-teleport="body">
                    <div x-show="confirm" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-transition.opacity>
                        <div class="fixed inset-0 bg-slate-900/50" @click="confirm = false"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl max-w-sm w-full p-6" @keydown.escape.window="confirm = false">
                            <h3 class="text-base font-bold text-slate-800">¿Limpiar bitácora antigua?</h3>
                            <p class="mt-1 text-sm text-slate-500">Se eliminarán {{ number_format($bitacoraAntigua) }} registro(s) con más de 90 días. Esta acción no se puede deshacer.</p>
                            <div class="mt-6 flex gap-3">
                                <button type="button" @click="confirm = false" class="flex-1 rounded-lg border border-slate-200 text-slate-600 text-sm font-medium py-2.5 hover:bg-slate-50">Cancelar</button>
                                <form method="POST" action="{{ route('mantenimiento.bitacora') }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold py-2.5">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
