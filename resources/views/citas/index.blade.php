@extends('layouts.app')

@section('title', 'Citas')
@section('subtitle', 'Agenda y gestión de citas médicas')

@section('content')
<div class="space-y-5">

    {{-- Filtros + acción --}}
    <div class="flex flex-col xl:flex-row xl:items-center gap-3">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar paciente..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
            <select name="estado" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Todos los estados</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected($estadoSel === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()"
                       class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 flex-1">
                <a href="{{ route('citas.index') }}" class="inline-flex items-center px-3 rounded-lg border border-slate-200 text-slate-500 text-xs font-medium hover:bg-slate-50">Limpiar</a>
            </div>
        </form>
        <a href="{{ route('citas.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm shrink-0">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva cita
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Fecha / Hora</th>
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Médico</th>
                        <th class="px-5 py-3">Motivo</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($citas as $cita)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <p class="font-medium text-slate-800">{{ $cita->fecha->translatedFormat('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }} h</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-medium text-slate-700">{{ $cita->paciente->nombre_completo ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                <p>{{ $cita->medico->nombre_completo ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $cita->medico->especialidad->nombre ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-500 max-w-[200px] truncate">{{ $cita->motivo ?: '—' }}</td>
                            <td class="px-5 py-3">@include('partials.cita-estado-menu', ['cita' => $cita])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('citas.show', $cita) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 text-xs font-medium">Ver</a>
                                    <a href="{{ route('citas.edit', $cita) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('citas.destroy', $cita), 'nombre' => 'la cita de '.($cita->paciente->nombre_completo ?? '')])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No se encontraron citas con los filtros aplicados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $citas->links() }}</div>
</div>
@endsection
