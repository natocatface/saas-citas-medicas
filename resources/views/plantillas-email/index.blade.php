@extends('layouts.app')

@section('title', 'Plantillas de Email')
@section('subtitle', 'Correos predefinidos de la clínica')

@php
    $tipoBadge = [
        'general' => 'bg-slate-100 text-slate-600',
        'recordatorio' => 'bg-amber-100 text-amber-700',
        'bienvenida' => 'bg-emerald-100 text-emerald-700',
        'confirmacion' => 'bg-sky-100 text-sky-700',
    ];
@endphp

@section('content')
<div class="space-y-5">

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $plantillas->total() }} plantillas</p>
        <a href="{{ route('plantillas-email.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva plantilla
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Nombre</th>
                        <th class="px-5 py-3">Tipo</th>
                        <th class="px-5 py-3">Asunto</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($plantillas as $p)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $p->nombre }}</td>
                            <td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $tipoBadge[$p->tipo] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($p->tipo) }}</span></td>
                            <td class="px-5 py-3 text-slate-500 max-w-[280px] truncate">{{ $p->asunto }}</td>
                            <td class="px-5 py-3 text-center">@include('partials.badge-estado', ['activo' => $p->activo])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('plantillas-email.edit', $p) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('plantillas-email.destroy', $p), 'nombre' => $p->nombre])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No hay plantillas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $plantillas->links() }}</div>
</div>
@endsection
