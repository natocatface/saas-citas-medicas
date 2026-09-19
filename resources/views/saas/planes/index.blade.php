@extends('layouts.app')

@section('title', 'Planes')
@section('subtitle', 'Planes de suscripción')

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $planes->total() }} planes configurados</p>
        <a href="{{ route('saas.planes.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nuevo plan
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($planes as $plan)
            <div class="relative bg-white rounded-2xl p-6 flex flex-col {{ $plan->destacado ? 'ring-2 ring-brand-500 shadow-lg' : 'border border-slate-200 shadow-sm' }}">
                @if($plan->destacado)
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-cyanx-500 to-brand-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">Destacado</span>
                @endif
                <div class="flex items-start justify-between">
                    <h3 class="font-bold text-slate-800 text-lg">{{ $plan->nombre }}</h3>
                    @include('partials.badge-estado', ['activo' => $plan->activo])
                </div>
                <div class="mt-2 flex items-end gap-1">
                    <span class="text-3xl font-black text-slate-900">S/ {{ number_format($plan->precio, 0) }}</span>
                    <span class="text-slate-400 mb-1 text-sm">/{{ $plan->periodo }}</span>
                </div>
                <p class="mt-1 text-xs text-slate-400">
                    {{ $plan->max_medicos ? $plan->max_medicos.' médicos' : 'Médicos ilimitados' }} ·
                    {{ $plan->max_usuarios ? $plan->max_usuarios.' usuarios' : 'Usuarios ilimitados' }}
                </p>
                <ul class="mt-4 space-y-2 flex-1">
                    @foreach($plan->caracteristicasLista() as $car)
                        <li class="flex items-start gap-2 text-sm text-slate-600">
                            <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            {{ $car }}
                        </li>
                    @endforeach
                </ul>
                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">{{ $plan->clinicas_count }} clínica(s)</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('saas.planes.edit', $plan) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                        @include('partials.boton-eliminar', ['action' => route('saas.planes.destroy', $plan), 'nombre' => 'el plan '.$plan->nombre])
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-400">No hay planes.</p>
        @endforelse
    </div>

    <div>{{ $planes->links() }}</div>
</div>
@endsection
