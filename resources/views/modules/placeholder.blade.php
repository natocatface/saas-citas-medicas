@extends('layouts.app')

@section('title', $titulo)
@section('subtitle', 'Módulo del sistema')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center">
            @include('partials.icon', ['name' => 'cog', 'class' => 'w-8 h-8'])
        </div>
        <h2 class="mt-5 text-xl font-bold text-slate-800">{{ $titulo }}</h2>
        <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">
            Este módulo está listo para ser desarrollado. La estructura, el menú y la navegación
            ya funcionan; aquí irá el CRUD y la lógica de <strong>{{ $titulo }}</strong>.
        </p>
        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
            En construcción
        </div>
        <div class="mt-8">
            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">← Volver al Dashboard</a>
        </div>
    </div>
</div>
@endsection
