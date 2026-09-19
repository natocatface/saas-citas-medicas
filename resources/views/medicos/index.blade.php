@extends('layouts.app')

@section('title', 'Médicos')
@section('subtitle', 'Directorio del personal médico')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 flex-1 max-w-md">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre o documento..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
            <select name="especialidad_id" onchange="this.form.submit()"
                    class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-56">
                <option value="">Todas las especialidades</option>
                @foreach($especialidades as $esp)
                    <option value="{{ $esp->id }}" @selected($especialidadId == $esp->id)>{{ $esp->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="hidden sm:inline-flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 text-sm font-medium px-4 hover:bg-slate-50">Filtrar</button>
        </form>
        <a href="{{ route('medicos.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm shrink-0">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nuevo médico
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Médico</th>
                        <th class="px-5 py-3">Especialidad</th>
                        <th class="px-5 py-3">Contacto</th>
                        <th class="px-5 py-3">Colegiatura</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($medicos as $medico)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-50 text-brand-600 text-xs font-bold flex items-center justify-center shrink-0">
                                        {{ mb_strtoupper(mb_substr($medico->nombres,0,1).mb_substr($medico->apellidos,0,1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $medico->nombre_completo }}</p>
                                        <p class="text-xs text-slate-400">{{ $medico->documento ?: 'Sin documento' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if($medico->especialidad)
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $medico->especialidad->color }}"></span>
                                        <span class="text-slate-600">{{ $medico->especialidad->nombre }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                <p>{{ $medico->telefono ?: '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $medico->email ?: '' }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $medico->numero_colegiatura ?: '—' }}</td>
                            <td class="px-5 py-3 text-center">@include('partials.badge-estado', ['activo' => $medico->activo])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('medicos.edit', $medico) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('medicos.destroy', $medico), 'nombre' => $medico->nombre_completo])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No se encontraron médicos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $medicos->links() }}</div>
</div>
@endsection
