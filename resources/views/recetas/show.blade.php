@extends('layouts.app')

@section('title', 'Receta ' . $receta->numero)
@section('subtitle', 'Detalle de la prescripción')

@section('content')
<div class="max-w-3xl space-y-4">

    <div class="flex items-center gap-3 print:hidden">
        <a href="{{ route('recetas.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Volver</a>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('recetas.edit', $receta) }}" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">Editar</a>
            <button onclick="window.print()" class="border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-4 py-2">Imprimir</button>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-cyan-400 to-brand-500 text-white flex items-center justify-center">
                    @include('partials.icon', ['name' => 'document', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <p class="font-bold text-slate-800 text-lg">Receta médica</p>
                    <p class="text-xs text-slate-400">{{ $receta->numero }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-slate-700">{{ $receta->fecha->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6">
            <div>
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Paciente</p>
                <p class="font-semibold text-slate-800">{{ $receta->paciente->nombre_completo ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $receta->paciente->documento ?? '' }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Médico</p>
                <p class="font-semibold text-slate-800">{{ $receta->medico->nombre_completo ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $receta->medico->especialidad->nombre ?? '' }}</p>
            </div>
            @if($receta->diagnostico)
                <div class="sm:col-span-2">
                    <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Diagnóstico</p>
                    <p class="text-slate-700">{{ $receta->diagnostico }}</p>
                </div>
            @endif
        </div>

        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-2">Medicamentos</p>
        <div class="space-y-2">
            @forelse($receta->items as $it)
                <div class="rounded-xl border border-slate-200 p-3">
                    <p class="font-semibold text-slate-800">{{ $it->medicamento }}</p>
                    <p class="text-sm text-slate-500">
                        {{ collect([$it->dosis, $it->frecuencia, $it->duracion])->filter()->join(' · ') ?: 'Sin detalle' }}
                    </p>
                    @if($it->indicaciones)<p class="text-xs text-slate-400 mt-0.5">{{ $it->indicaciones }}</p>@endif
                </div>
            @empty
                <p class="text-sm text-slate-400">Sin medicamentos.</p>
            @endforelse
        </div>

        @if($receta->indicaciones)
            <div class="mt-6 pt-4 border-t border-slate-100">
                <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1">Indicaciones generales</p>
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $receta->indicaciones }}</p>
            </div>
        @endif

        <div class="mt-10 pt-4 flex justify-end">
            <div class="text-center">
                <div class="w-48 border-t border-slate-300"></div>
                <p class="text-xs text-slate-500 mt-1">{{ $receta->medico->nombre_completo ?? '' }}</p>
                <p class="text-[10px] text-slate-400">{{ $receta->medico->numero_colegiatura ? 'Col. '.$receta->medico->numero_colegiatura : '' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
