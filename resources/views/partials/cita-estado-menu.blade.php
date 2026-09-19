{{-- Badge de estado con menú para cambiar estado rápido. Var: $cita --}}
@php
    $map = [
        'pendiente'  => ['bg-amber-100 text-amber-700', 'bg-amber-500', 'Pendiente'],
        'confirmada' => ['bg-sky-100 text-sky-700', 'bg-sky-500', 'Confirmada'],
        'atendida'   => ['bg-emerald-100 text-emerald-700', 'bg-emerald-500', 'Atendida'],
        'cancelada'  => ['bg-rose-100 text-rose-700', 'bg-rose-500', 'Cancelada'],
    ];
    $estados = ['pendiente','confirmada','atendida','cancelada'];
    [$cls, $dot, $label] = $map[$cita->estado] ?? ['bg-slate-100 text-slate-600','bg-slate-400',ucfirst($cita->estado)];
@endphp
<div x-data="{ open: false }" class="relative inline-block">
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $cls }} hover:opacity-80">
        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span> {{ $label }}
        <svg class="w-3 h-3 opacity-60" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
    </button>
    <div x-show="open" x-cloak x-transition
         class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-20">
        @foreach($estados as $e)
            <form method="POST" action="{{ route('citas.estado', $cita) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="estado" value="{{ $e }}">
                <button type="submit" class="w-full text-left px-3 py-1.5 text-xs hover:bg-slate-50 flex items-center gap-2 {{ $cita->estado === $e ? 'font-semibold text-slate-800' : 'text-slate-600' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $map[$e][1] }}"></span> {{ $map[$e][2] }}
                </button>
            </form>
        @endforeach
    </div>
</div>
