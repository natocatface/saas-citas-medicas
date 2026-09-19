@extends('layouts.app')

@section('title', 'Bitácora')
@section('subtitle', 'Registro de auditoría de la plataforma')

@section('content')
<div class="space-y-5">

    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 flex-1 max-w-sm">
            <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
            <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por usuario o acción..."
                   class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
        </div>
        <select name="clinica_id" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-56">
            <option value="">Todas las clínicas</option>
            @foreach($clinicas as $cl)
                <option value="{{ $cl->id }}" @selected($clinicaSel == $cl->id)>{{ $cl->nombre }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg border border-slate-200 text-slate-600 text-sm font-medium px-4 hover:bg-slate-50">Filtrar</button>
    </form>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3">Usuario</th>
                        <th class="px-5 py-3">Acción</th>
                        <th class="px-5 py-3">Módulo</th>
                        <th class="px-5 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($registros as $r)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $r->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $r->usuario_nombre ?: 'Sistema' }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $r->accion }}</td>
                            <td class="px-5 py-3 text-slate-400">{{ $r->modelo ? $r->modelo.' #'.$r->modelo_id : '—' }}</td>
                            <td class="px-5 py-3 text-slate-400 font-mono text-xs">{{ $r->ip ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Sin registros de actividad.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $registros->links() }}</div>
</div>
@endsection
