@extends('layouts.app')

@section('title', 'Recetas Médicas')
@section('subtitle', 'Prescripciones emitidas')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex-1 max-w-md">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por N° o paciente..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
        </form>
        <a href="{{ route('recetas.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva receta
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">N°</th>
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Médico</th>
                        <th class="px-5 py-3">Diagnóstico</th>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recetas as $r)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $r->numero }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $r->paciente->nombre_completo ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $r->medico->nombre_completo ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500 max-w-[220px] truncate">{{ $r->diagnostico ?: '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $r->fecha->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('recetas.show', $r) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 text-xs font-medium">Ver</a>
                                    <a href="{{ route('recetas.edit', $r) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('recetas.destroy', $r), 'nombre' => 'la receta '.$r->numero])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No hay recetas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $recetas->links() }}</div>
</div>
@endsection
