@extends('layouts.app')

@section('title', 'Lista de Espera')
@section('subtitle', 'Pacientes en espera de atención')

@php
    $prioBadge = [
        'baja'    => 'bg-slate-100 text-slate-600',
        'media'   => 'bg-sky-100 text-sky-700',
        'alta'    => 'bg-amber-100 text-amber-700',
        'urgente' => 'bg-rose-100 text-rose-700',
    ];
    $estBadge = [
        'esperando' => 'bg-amber-100 text-amber-700',
        'llamado'   => 'bg-sky-100 text-sky-700',
        'atendido'  => 'bg-emerald-100 text-emerald-700',
        'cancelado' => 'bg-slate-200 text-slate-500',
    ];
@endphp

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex-1">
            <select name="estado" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-56">
                <option value="">Activos (sin cancelados)</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected($estadoSel === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('lista-espera.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm shrink-0">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Agregar a la lista
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Especialidad / Médico</th>
                        <th class="px-5 py-3">Motivo</th>
                        <th class="px-5 py-3 text-center">Prioridad</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3">En espera</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registros as $r)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $r->paciente->nombre_completo ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                <p>{{ $r->especialidad->nombre ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $r->medico->nombre_completo ?? 'Sin médico asignado' }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-500 max-w-[200px] truncate">{{ $r->motivo ?: '—' }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $prioBadge[$r->prioridad] ?? '' }}">{{ ucfirst($r->prioridad) }}</span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $estBadge[$r->estado] ?? '' }}">{{ ucfirst($r->estado) }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-400 text-xs">{{ $r->created_at?->diffForHumans() }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(in_array($r->estado, ['esperando','llamado']))
                                        <form method="POST" action="{{ route('lista-espera.convertir', $r) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 text-xs font-medium">→ Cita</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('lista-espera.edit', $r) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('lista-espera.destroy', $r), 'nombre' => 'a '.($r->paciente->nombre_completo ?? '')])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No hay pacientes en espera.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $registros->links() }}</div>
</div>
@endsection
