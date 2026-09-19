@php
    $map = [
        'prueba'     => ['bg-amber-100 text-amber-700', 'Prueba'],
        'activa'     => ['bg-emerald-100 text-emerald-700', 'Activa'],
        'suspendida' => ['bg-rose-100 text-rose-700', 'Suspendida'],
        'cancelada'  => ['bg-slate-200 text-slate-500', 'Cancelada'],
    ];
    [$cls, $label] = $map[$estado] ?? ['bg-slate-100 text-slate-600', ucfirst($estado)];
@endphp
<span class="px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0 {{ $cls }}">{{ $label }}</span>
