@extends('layouts.app')

@section('title', 'Plantillas CIE-10')
@section('subtitle', 'Catálogo de diagnósticos')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex-1 max-w-md">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por código o descripción..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
        </form>
        <a href="{{ route('cie10.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nuevo diagnóstico
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Código</th>
                        <th class="px-5 py-3">Descripción</th>
                        <th class="px-5 py-3">Categoría</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $it)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3"><span class="font-mono font-semibold text-brand-700">{{ $it->codigo }}</span></td>
                            <td class="px-5 py-3 text-slate-700">{{ $it->descripcion }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $it->categoria ?: '—' }}</td>
                            <td class="px-5 py-3 text-center">@include('partials.badge-estado', ['activo' => $it->activo])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('cie10.edit', $it) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('cie10.destroy', $it), 'nombre' => $it->codigo])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No hay diagnósticos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $items->links() }}</div>
</div>
@endsection
