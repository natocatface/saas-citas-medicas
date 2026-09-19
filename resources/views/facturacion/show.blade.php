@extends('layouts.app')

@section('title', 'Factura ' . $factura->numero)
@section('subtitle', 'Detalle del comprobante')

@php
    $estadoBadge = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'pagada'    => 'bg-emerald-100 text-emerald-700',
        'anulada'   => 'bg-slate-200 text-slate-500',
    ];
    $cpe = $factura->comprobanteElectronico;
    $cpeBadge = [
        'aceptado'  => 'bg-emerald-100 text-emerald-700',
        'observado' => 'bg-amber-100 text-amber-700',
        'enviado'   => 'bg-sky-100 text-sky-700',
        'pendiente' => 'bg-sky-100 text-sky-700',
        'error'     => 'bg-rose-100 text-rose-700',
        'rechazado' => 'bg-rose-100 text-rose-700',
        'anulado'   => 'bg-slate-200 text-slate-500',
    ];
@endphp

@section('content')
<div class="max-w-3xl space-y-4">

    <div class="flex items-center gap-3 print:hidden">
        <a href="{{ route('facturacion.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Volver</a>
        <div class="ml-auto flex items-center gap-2">
            @if($factura->estado === 'pendiente')
                <form method="POST" action="{{ route('facturacion.pagar', $factura) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm">Marcar como pagada</button>
                </form>
            @endif
            <a href="{{ route('facturacion.edit', $factura) }}" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">Editar</a>
            <button onclick="window.print()" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">Imprimir</button>
        </div>
    </div>

    {{-- Comprobante --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 to-brand-500 text-white flex items-center justify-center">
                    @include('partials.icon', ['name' => 'logo', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <p class="font-bold text-slate-800 text-lg">CitasMédicas</p>
                    <p class="text-xs text-slate-400">Comprobante de servicios médicos</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-slate-800">{{ $factura->numero }}</p>
                <p class="text-xs text-slate-400">{{ $factura->fecha->format('d/m/Y') }}</p>
                <span class="inline-block mt-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $estadoBadge[$factura->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($factura->estado) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6">
            <div>
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Paciente</p>
                <p class="font-semibold text-slate-800">{{ $factura->paciente->nombre_completo ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $factura->paciente->documento ?? '' }}</p>
                <p class="text-sm text-slate-400">{{ $factura->paciente->aseguradora->nombre ?? 'Particular' }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Método de pago</p>
                <p class="font-semibold text-slate-800">{{ $factura->metodo_pago ?: '—' }}</p>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase border-y border-slate-200">
                    <th class="py-2 pr-3">Descripción</th>
                    <th class="py-2 px-3 text-right w-20">Cant.</th>
                    <th class="py-2 px-3 text-right w-28">P. Unit.</th>
                    <th class="py-2 pl-3 text-right w-28">Importe</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($factura->items as $it)
                    <tr>
                        <td class="py-2.5 pr-3 text-slate-700">{{ $it->descripcion }}</td>
                        <td class="py-2.5 px-3 text-right text-slate-500">{{ rtrim(rtrim(number_format($it->cantidad, 2), '0'), '.') }}</td>
                        <td class="py-2.5 px-3 text-right text-slate-500">S/ {{ number_format($it->precio_unitario, 2) }}</td>
                        <td class="py-2.5 pl-3 text-right font-medium text-slate-700">S/ {{ number_format($it->importe, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-slate-400">Sin ítems.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-5 flex justify-end">
            <div class="w-full sm:w-64 space-y-1.5 text-sm">
                <div class="flex justify-between text-slate-500"><span>Subtotal</span><span class="text-slate-700">S/ {{ number_format($factura->subtotal, 2) }}</span></div>
                <div class="flex justify-between text-slate-500"><span>Descuento</span><span>- S/ {{ number_format($factura->descuento, 2) }}</span></div>
                <div class="flex justify-between text-slate-500"><span>Impuesto</span><span>S/ {{ number_format($factura->impuesto, 2) }}</span></div>
                <div class="flex justify-between pt-2 border-t border-slate-200 text-base"><span class="font-semibold text-slate-700">Total</span><span class="font-bold text-slate-900">S/ {{ number_format($factura->total, 2) }}</span></div>
            </div>
        </div>

        @if($factura->notas)
            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Notas</p>
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $factura->notas }}</p>
            </div>
        @endif
    </div>

    {{-- Facturación electrónica --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 print:hidden">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-400 to-brand-500 text-white flex items-center justify-center text-sm font-bold">e</span>
            <div>
                <p class="font-semibold text-slate-800">Comprobante electrónico</p>
                <p class="text-xs text-slate-400">{{ strtoupper(config('facturacion.pais_defecto', 'PE')) }} · {{ config('facturacion.proveedores.'.config('facturacion.pais_defecto','PE').'.habilitado') ? 'Producción' : 'Modo simulado' }}</p>
            </div>
        </div>

        @if($cpe)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Número</p>
                    <p class="font-semibold text-slate-800">{{ $cpe->numero_completo }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Estado</p>
                    <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $cpeBadge[$cpe->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($cpe->estado) }}</span>
                </div>
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Tipo</p>
                    <p class="font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $cpe->tipo)) }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Hash</p>
                    <p class="font-mono text-xs text-slate-500 truncate" title="{{ $cpe->hash_cpe }}">{{ $cpe->hash_cpe ? \Illuminate\Support\Str::limit($cpe->hash_cpe, 16) : '—' }}</p>
                </div>
            </div>
            @if($cpe->mensaje)
                <p class="mt-3 text-xs text-slate-500">{{ $cpe->mensaje }}</p>
            @endif
            @php
                $puedeNc = in_array($cpe->estado, ['aceptado', 'observado']) && in_array($cpe->tipo, ['factura', 'boleta']);
            @endphp
            <div x-data="{ nc: false, anular: false }" class="mt-4 flex flex-wrap items-center gap-2">
                <a href="{{ route('facturacion.cpe-print', $factura) }}" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-lg px-4 py-2">Representación impresa (PDF)</a>
                @if($cpe->ruta_xml)
                    <a href="{{ route('facturacion.cpe-xml', $factura) }}" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">Descargar XML</a>
                @endif
                <form method="POST" action="{{ route('facturacion.consultar-cpe', $factura) }}">
                    @csrf
                    <button type="submit" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">Consultar estado</button>
                </form>
                @if(in_array($cpe->estado, ['error', 'rechazado']))
                    <form method="POST" action="{{ route('facturacion.emitir-cpe', $factura) }}">
                        @csrf
                        <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm">Reintentar emisión</button>
                    </form>
                @endif

                @if($cpe->estado === 'pendiente' && $cpe->ticket)
                    <form method="POST" action="{{ route('facturacion.consultar-baja', $factura) }}">
                        @csrf
                        <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm">Consultar baja (SUNAT)</button>
                    </form>
                @endif

                @if($puedeNc)
                    <button type="button" @click="nc = true" class="border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 text-sm font-semibold rounded-lg px-4 py-2">Nota de crédito</button>
                    <button type="button" @click="anular = true" class="border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 text-sm font-semibold rounded-lg px-4 py-2">Anular</button>

                    {{-- ===== Modal: Nota de crédito ===== --}}
                    <div x-cloak x-show="nc" @keydown.escape.window="nc = false" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/50" @click="nc = false"></div>
                        <form method="POST" action="{{ route('facturacion.nota-credito', $factura) }}" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
                            @csrf
                            <h4 class="font-bold text-slate-800 text-lg">Emitir nota de crédito</h4>
                            <p class="text-xs text-slate-400 mb-4">Afecta al comprobante {{ $cpe->numero_completo }}.</p>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Motivo (catálogo SUNAT 09)</label>
                            <select name="codigo_motivo" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 mb-3">
                                <option value="01">01 — Anulación de la operación</option>
                                <option value="02">02 — Anulación por error en el RUC</option>
                                <option value="03">03 — Corrección por error en la descripción</option>
                                <option value="06">06 — Devolución total</option>
                                <option value="07">07 — Devolución por ítem</option>
                                <option value="09">09 — Disminución en el valor</option>
                            </select>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Sustento / descripción</label>
                            <textarea name="motivo" rows="2" required maxlength="250" placeholder="Ej. Anulación de la operación por solicitud del cliente."
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button" @click="nc = false" class="text-sm font-medium text-slate-500 hover:text-slate-700 px-4 py-2">Cancelar</button>
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Emitir nota de crédito</button>
                            </div>
                        </form>
                    </div>

                    {{-- ===== Modal: Anular ===== --}}
                    <div x-cloak x-show="anular" @keydown.escape.window="anular = false" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/50" @click="anular = false"></div>
                        <form method="POST" action="{{ route('facturacion.anular-cpe', $factura) }}" class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
                            @csrf
                            <h4 class="font-bold text-slate-800 text-lg">Anular comprobante</h4>
                            <p class="text-xs text-slate-400 mb-4">Da de baja el comprobante {{ $cpe->numero_completo }} ante SUNAT
                                @if($cpe->tipo === 'boleta') mediante el Resumen Diario (RC, estado 3). @else mediante la Comunicación de Baja (RA). @endif
                                Es un proceso asíncrono: si queda en proceso, confírmalo con “Consultar baja”.</p>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Motivo de la baja</label>
                            <textarea name="motivo" rows="2" required maxlength="250" placeholder="Ej. Error en los datos del comprobante."
                                      class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                            <div class="mt-5 flex justify-end gap-2">
                                <button type="button" @click="anular = false" class="text-sm font-medium text-slate-500 hover:text-slate-700 px-4 py-2">Cancelar</button>
                                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Anular comprobante</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @else
            <p class="text-sm text-slate-500 mb-4">Esta factura aún no tiene comprobante electrónico emitido ante el organismo tributario.</p>
            @if($factura->estado !== 'anulada')
                <form method="POST" action="{{ route('facturacion.emitir-cpe', $factura) }}">
                    @csrf
                    <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Emitir comprobante electrónico</button>
                </form>
            @else
                <p class="text-xs text-slate-400">Factura anulada — no se puede emitir.</p>
            @endif
        @endif
    </div>
</div>
@endsection
