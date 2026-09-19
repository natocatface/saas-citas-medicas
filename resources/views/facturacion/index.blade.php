@extends('layouts.app')

@section('title', 'Facturación')
@section('subtitle', 'Facturas y cobros')

@php
    $estadoBadge = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'pagada'    => 'bg-emerald-100 text-emerald-700',
        'anulada'   => 'bg-slate-200 text-slate-500',
    ];
@endphp

@section('content')
<div class="space-y-5">

    {{-- Resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-50 to-white border border-slate-200/70 rounded-2xl shadow-sm p-5">
            <p class="text-[11px] font-semibold tracking-wider text-slate-500">COBRADO</p>
            <p class="text-2xl font-bold text-emerald-600 mt-2">S/ {{ number_format($totales['pagado'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-white border border-slate-200/70 rounded-2xl shadow-sm p-5">
            <p class="text-[11px] font-semibold tracking-wider text-slate-500">POR COBRAR</p>
            <p class="text-2xl font-bold text-amber-600 mt-2">S/ {{ number_format($totales['pendiente'], 2) }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <p class="text-[11px] font-semibold tracking-wider text-slate-400">FACTURAS</p>
            <p class="text-2xl font-bold text-slate-800 mt-2">{{ number_format($totales['count']) }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 flex-1 max-w-md">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por N° o paciente..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
            <select name="estado" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-48">
                <option value="">Todos los estados</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected($estadoSel === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('facturacion.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm shrink-0">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nueva factura
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">N°</th>
                        <th class="px-5 py-3">Paciente</th>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($facturas as $f)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $f->numero }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $f->paciente->nombre_completo ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $f->fecha->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-700">S/ {{ number_format($f->total, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $estadoBadge[$f->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($f->estado) }}</span>
                                @if($cpe = $f->comprobanteElectronico)
                                    @php $ok = in_array($cpe->estado, ['aceptado', 'observado']); @endphp
                                    <span class="block mt-1 text-[10px] font-semibold {{ $ok ? 'text-emerald-600' : 'text-slate-400' }}" title="Comprobante electrónico: {{ $cpe->estado }}">
                                        CPE · {{ ucfirst($cpe->estado) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if($f->estado === 'pendiente')
                                        <form method="POST" action="{{ route('facturacion.pagar', $f) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 text-xs font-medium">Cobrar</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('facturacion.show', $f) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 text-xs font-medium">Ver</a>
                                    <a href="{{ route('facturacion.edit', $f) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @include('partials.boton-eliminar', ['action' => route('facturacion.destroy', $f), 'nombre' => 'la factura '.$f->numero])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">No hay facturas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $facturas->links() }}</div>
</div>
@endsection
