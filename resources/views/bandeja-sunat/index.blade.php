@extends('layouts.app')

@section('title', 'Comprobantes SUNAT')
@section('subtitle', 'Monitoreo y reproceso de comprobantes electrónicos')

@php
    $badge = [
        'aceptado'  => 'bg-emerald-100 text-emerald-700',
        'observado' => 'bg-amber-100 text-amber-700',
        'enviado'   => 'bg-sky-100 text-sky-700',
        'pendiente' => 'bg-sky-100 text-sky-700',
        'error'     => 'bg-rose-100 text-rose-700',
        'rechazado' => 'bg-rose-100 text-rose-700',
        'anulado'   => 'bg-slate-200 text-slate-500',
        'borrador'  => 'bg-slate-100 text-slate-500',
    ];
    $tipoLabel = ['factura' => 'Factura', 'boleta' => 'Boleta', 'nota_credito' => 'N. Crédito', 'nota_debito' => 'N. Débito'];
@endphp

@section('content')
<div class="space-y-5">

    {{-- Resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-50 to-white border border-slate-200/70 rounded-2xl shadow-sm p-5">
            <p class="text-[11px] font-semibold tracking-wider text-slate-500">ACEPTADOS</p>
            <p class="text-2xl font-bold text-emerald-600 mt-2">{{ number_format($resumen['aceptado']) }}</p>
        </div>
        <div class="bg-gradient-to-br from-sky-50 to-white border border-slate-200/70 rounded-2xl shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold tracking-wider text-slate-500">PENDIENTES</p>
                <p class="text-2xl font-bold text-sky-600 mt-2">{{ number_format($resumen['pendiente']) }}</p>
                @if(($resumen['bajas'] ?? 0) > 0)
                    <p class="text-[11px] text-slate-500 mt-1">{{ number_format($resumen['bajas']) }} baja(s) en proceso</p>
                @endif
            </div>
            @if(($resumen['bajas'] ?? 0) > 0)
                <form method="POST" action="{{ route('bandeja-sunat.consultar-bajas') }}"
                      onsubmit="return confirm('¿Consultar en SUNAT todas las bajas en proceso?');">
                    @csrf
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg px-3 py-2 shadow-sm">Consultar bajas</button>
                </form>
            @endif
        </div>
        <div class="bg-gradient-to-br from-rose-50 to-white border border-slate-200/70 rounded-2xl shadow-sm p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold tracking-wider text-slate-500">EN ERROR</p>
                <p class="text-2xl font-bold text-rose-600 mt-2">{{ number_format($resumen['error']) }}</p>
            </div>
            @if($resumen['error'] > 0)
                <form method="POST" action="{{ route('bandeja-sunat.reprocesar') }}"
                      onsubmit="return confirm('¿Reprocesar todos los comprobantes en error / pendientes?');">
                    @csrf
                    <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg px-3 py-2 shadow-sm">Reprocesar</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 flex-1 max-w-md">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por N°, receptor o documento..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
            <select name="estado" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-48">
                <option value="">Todos los estados</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected($estadoSel === $e)>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Comprobante</th>
                        <th class="px-5 py-3">Receptor</th>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($comprobantes as $c)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-700">{{ $c->numero_completo }}</p>
                                <p class="text-[11px] text-slate-400">{{ $tipoLabel[$c->tipo] ?? ucfirst($c->tipo) }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-600">
                                {{ $c->receptor_nombre ?: '—' }}
                                <span class="block text-[11px] text-slate-400">{{ $c->receptor_numero }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ optional($c->fecha_emision)->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-700">S/ {{ number_format($c->total, 2) }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge[$c->estado] ?? 'bg-slate-100 text-slate-600' }}"
                                      @if($c->mensaje) title="{{ $c->mensaje }}" @endif>{{ ucfirst($c->estado) }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    @if(in_array($c->estado, ['error', 'rechazado', 'pendiente', 'borrador']))
                                        <form method="POST" action="{{ route('bandeja-sunat.reintentar', $c) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 text-xs font-medium">Reintentar</button>
                                        </form>
                                    @endif
                                    @if($c->factura_id)
                                        <a href="{{ route('facturacion.cpe-print', $c->factura_id) }}" target="_blank" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 text-xs font-medium">PDF</a>
                                    @endif
                                    @if($c->ruta_xml && $c->factura_id)
                                        <a href="{{ route('facturacion.cpe-xml', $c->factura_id) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 text-xs font-medium">XML</a>
                                    @endif
                                    @if($c->factura_id)
                                        <a href="{{ route('facturacion.show', $c->factura_id) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 text-xs font-medium">Ver</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-400">Aún no hay comprobantes electrónicos emitidos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $comprobantes->links() }}</div>
</div>
@endsection
