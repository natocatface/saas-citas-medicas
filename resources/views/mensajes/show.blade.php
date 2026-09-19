@extends('layouts.app')

@section('title', 'Mensaje')
@section('subtitle', $mensaje->asunto)

@section('content')
<div class="max-w-2xl space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('mensajes.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">← Volver</a>
        <form method="POST" action="{{ route('mensajes.destroy', $mensaje) }}" class="ml-auto"
              onsubmit="return confirm('¿Eliminar este mensaje?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700">Eliminar</button>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <h2 class="text-xl font-bold text-slate-800">{{ $mensaje->asunto }}</h2>
        <div class="flex items-center gap-3 mt-4 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyanx-500 to-brand-600 text-white text-sm font-bold flex items-center justify-center">
                {{ $mensaje->remitente?->iniciales() ?? '?' }}
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">{{ $mensaje->remitente?->name ?? 'Usuario' }}</p>
                <p class="text-xs text-slate-400">Para: {{ $mensaje->destinatario?->name ?? '' }} · {{ $mensaje->created_at?->translatedFormat('d M Y, H:i') }}</p>
            </div>
        </div>
        <div class="mt-5 text-slate-700 whitespace-pre-line leading-relaxed">{{ $mensaje->cuerpo }}</div>
    </div>

    @if($mensaje->remitente && $mensaje->remitente_id !== auth()->id())
        <a href="{{ route('mensajes.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
            @include('partials.icon', ['name' => 'chat', 'class' => 'w-4 h-4']) Redactar nuevo mensaje
        </a>
    @endif
</div>
@endsection
