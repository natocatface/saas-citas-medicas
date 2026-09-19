@extends('layouts.app')

@section('title', 'Detalle de cita')
@section('subtitle', $cita->fecha->translatedFormat('d \d\e F, Y'))

@section('content')
<div class="max-w-3xl space-y-5">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-6 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-4">
                <div class="text-center bg-white border border-slate-200 rounded-xl px-4 py-2 shrink-0">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase">{{ $cita->fecha->translatedFormat('M') }}</p>
                    <p class="text-2xl font-bold text-slate-700 leading-none">{{ $cita->fecha->format('d') }}</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-slate-800">{{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }} h</p>
                    <p class="text-sm text-slate-400">{{ $cita->fecha->translatedFormat('l') }}</p>
                </div>
            </div>
            @include('partials.cita-estado-menu', ['cita' => $cita])
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6">
            <div>
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Paciente</p>
                <p class="font-semibold text-slate-800">{{ $cita->paciente->nombre_completo ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $cita->paciente->documento ?? '' }}</p>
                <p class="text-sm text-slate-500">{{ $cita->paciente->telefono ?? '' }}</p>
                <p class="text-sm text-slate-400">{{ $cita->paciente->aseguradora->nombre ?? 'Particular' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Médico</p>
                <p class="font-semibold text-slate-800">{{ $cita->medico->nombre_completo ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $cita->medico->especialidad->nombre ?? '' }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Motivo</p>
                <p class="text-slate-700">{{ $cita->motivo ?: 'Sin especificar' }}</p>
            </div>
            @if($cita->notas)
                <div class="sm:col-span-2">
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Notas</p>
                    <p class="text-slate-600 text-sm whitespace-pre-line">{{ $cita->notas }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('citas.edit', $cita) }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Editar cita</a>
        <a href="{{ route('citas.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Volver a la agenda</a>
    </div>
</div>
@endsection
