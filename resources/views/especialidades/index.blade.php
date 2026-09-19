@extends('layouts.app')

@section('title', 'Especialidades')
@section('subtitle', 'Catálogo de especialidades médicas')

@section('content')
<div class="space-y-5">

    {{-- Encabezado + acciones --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex-1 max-w-md">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar especialidad..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
        </form>
        <a href="{{ route('especialidades.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva especialidad
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Especialidad</th>
                        <th class="px-5 py-3">Descripción</th>
                        <th class="px-5 py-3 text-center">Médicos</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($especialidades as $esp)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-3.5 h-3.5 rounded-full shrink-0" style="background: {{ $esp->color }}"></span>
                                    <span class="font-medium text-slate-800">{{ $esp->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $esp->descripcion ?: '—' }}</td>
                            <td class="px-5 py-3 text-center text-slate-600">{{ $esp->medicos_count }}</td>
                            <td class="px-5 py-3 text-center">
                                @include('partials.badge-estado', ['activo' => $esp->activo])
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('especialidades.edit', $esp) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('especialidades.destroy', $esp), 'nombre' => $esp->nombre])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No se encontraron especialidades.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $especialidades->links() }}</div>
</div>
@endsection
