{{-- Vars: $factura, $pacientes, $estados, $metodos, $items, $action, $method --}}
@php
    $itemsInit = collect($items)->map(fn ($it) => [
        'descripcion' => $it['descripcion'] ?? $it->descripcion ?? '',
        'cantidad' => (float) ($it['cantidad'] ?? $it->cantidad ?? 1),
        'precio_unitario' => (float) ($it['precio_unitario'] ?? $it->precio_unitario ?? 0),
    ])->values();
@endphp

<div class="max-w-4xl">
    <form method="POST" action="{{ $action }}"
          x-data="{
              items: {{ Illuminate\Support\Js::from($itemsInit) }},
              descuento: {{ (float) old('descuento', $factura->descuento ?? 0) }},
              igvTasa: 0.18,
              add() { this.items.push({descripcion:'', cantidad:1, precio_unitario:0}); },
              remove(i) { this.items.splice(i,1); if(!this.items.length) this.add(); },
              imp(it) { return (parseFloat(it.cantidad||0) * parseFloat(it.precio_unitario||0)); },
              get subtotal() { return this.items.reduce((s,it)=>s+this.imp(it),0); },
              get baseImponible() { return Math.max(0, this.subtotal - parseFloat(this.descuento||0)); },
              get impuesto() { return this.baseImponible * this.igvTasa; },
              get total() { return this.baseImponible + this.impuesto; },
              money(n) { return (Math.round(n*100)/100).toLocaleString('es-PE',{minimumFractionDigits:2, maximumFractionDigits:2}); }
          }"
          x-init="if(!items.length) add()"
          class="space-y-5">
        @csrf
        @if(($method ?? 'POST') === 'PUT') @method('PUT') @endif

        {{-- Datos generales --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
                    <select name="paciente_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Seleccionar paciente —</option>
                        @foreach($pacientes as $p)
                            <option value="{{ $p->id }}" @selected(old('paciente_id', $factura->paciente_id) == $p->id)>{{ $p->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
                    <input type="date" name="fecha" value="{{ old('fecha', optional($factura->fecha)->format('Y-m-d')) }}" required
                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado</label>
                    <select name="estado" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        @foreach($estados as $e)
                            <option value="{{ $e }}" @selected(old('estado', $factura->estado) === $e)>{{ ucfirst($e) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Método de pago</label>
                    <select name="metodo_pago" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">— Sin definir —</option>
                        @foreach($metodos as $m)
                            <option value="{{ $m }}" @selected(old('metodo_pago', $factura->metodo_pago) === $m)>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Ítems --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Detalle</h3>
                <button type="button" @click="add()" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Agregar ítem
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[640px]">
                    <thead>
                        <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase border-b border-slate-200">
                            <th class="py-2 pr-3">Descripción</th>
                            <th class="py-2 px-3 w-24">Cant.</th>
                            <th class="py-2 px-3 w-32">P. Unit.</th>
                            <th class="py-2 px-3 w-32 text-right">Importe</th>
                            <th class="py-2 pl-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(it, i) in items" :key="i">
                            <tr class="border-b border-slate-50">
                                <td class="py-2 pr-3">
                                    <input type="text" x-model="it.descripcion" :name="`items[${i}][descripcion]`" placeholder="Concepto / servicio"
                                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" step="0.01" min="0" x-model="it.cantidad" :name="`items[${i}][cantidad]`"
                                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="number" step="0.01" min="0" x-model="it.precio_unitario" :name="`items[${i}][precio_unitario]`"
                                           class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                </td>
                                <td class="py-2 px-3 text-right font-medium text-slate-600" x-text="money(imp(it))"></td>
                                <td class="py-2 pl-3 text-center">
                                    <button type="button" @click="remove(i)" class="text-slate-400 hover:text-rose-500" title="Quitar">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Totales --}}
            <div class="mt-5 flex justify-end">
                <div class="w-full sm:w-72 space-y-2 text-sm">
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-medium text-slate-700">S/ <span x-text="money(subtotal)"></span></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Descuento</span>
                        <input type="number" step="0.01" min="0" name="descuento" x-model="descuento" class="w-28 rounded-lg border-slate-300 text-sm text-right focus:border-brand-500 focus:ring-brand-500">
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>IGV (18%)</span>
                        <span class="font-medium text-slate-700">S/ <span x-text="money(impuesto)"></span></span>
                    </div>
                    <input type="hidden" name="impuesto" :value="(Math.round(impuesto*100)/100).toFixed(2)">
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200 text-base">
                        <span class="font-semibold text-slate-700">Total</span>
                        <span class="font-bold text-slate-900">S/ <span x-text="money(total)"></span></span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
            <textarea name="notas" rows="2" class="w-full max-w-2xl rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notas', $factura->notas) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Guardar factura</button>
            <a href="{{ route('facturacion.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
        </div>
    </form>
</div>
