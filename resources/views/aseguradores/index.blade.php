@extends('layouts.app')

@section('title', 'Aseguradores')
@section('subtitle', 'Compañías aseguradoras y convenios')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <form method="GET" class="flex-1 max-w-md">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o RUC..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
        </form>
        <a href="{{ route('aseguradores.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva aseguradora
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Aseguradora</th>
                        <th class="px-5 py-3">RUC</th>
                        <th class="px-5 py-3">Contacto</th>
                        <th class="px-5 py-3 text-center">Pacientes</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($aseguradoras as $a)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                        @include('partials.icon', ['name' => 'shield', 'class' => 'w-5 h-5'])
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $a->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $a->ruc ?: '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">
                                <p>{{ $a->telefono ?: '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $a->email ?: '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-center text-slate-600">{{ $a->pacientes_count }}</td>
                            <td class="px-5 py-3 text-center">@include('partials.badge-estado', ['activo' => $a->activo])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('aseguradores.edit', $a) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('aseguradores.destroy', $a), 'nombre' => $a->nombre])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No se encontraron aseguradoras.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $aseguradoras->links() }}</div>
</div>
@endsection
