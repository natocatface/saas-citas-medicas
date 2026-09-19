@extends('layouts.app')

@section('title', 'Clínicas')
@section('subtitle', 'Cuentas de clínicas en la plataforma')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 flex-1 max-w-md">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar clínica..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
            <select name="estado" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-48">
                <option value="">Todos los estados</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected($estadoSel === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('saas.clinicas.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm shrink-0">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva clínica
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Clínica</th>
                        <th class="px-5 py-3">Plan</th>
                        <th class="px-5 py-3 text-center">Médicos</th>
                        <th class="px-5 py-3 text-center">Pacientes</th>
                        <th class="px-5 py-3">Vence</th>
                        <th class="px-5 py-3 text-center">Suscripción</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clinicas as $cl)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-lg flex items-center justify-center text-white shrink-0" style="background: {{ $cl->color }}">
                                        @include('partials.icon', ['name' => 'building', 'class' => 'w-5 h-5'])
                                    </span>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $cl->nombre }}</p>
                                        <p class="text-xs text-slate-400">{{ $cl->email ?: $cl->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $cl->plan->nombre ?? '—' }}</td>
                            <td class="px-5 py-3 text-center text-slate-600">{{ $cl->medicos_count }}</td>
                            <td class="px-5 py-3 text-center text-slate-600">{{ $cl->pacientes_count }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $cl->suscripcion_vence?->format('d/m/Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-center">@include('saas.partials.estado-suscripcion', ['estado' => $cl->estado_suscripcion])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('saas.clinicas.edit', $cl) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('saas.clinicas.destroy', $cl), 'nombre' => $cl->nombre])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">No hay clínicas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $clinicas->links() }}</div>
</div>
@endsection
