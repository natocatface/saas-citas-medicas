@php($activo = $activo ?? true)
@if($activo)
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">
        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactivo
    </span>
@endif
