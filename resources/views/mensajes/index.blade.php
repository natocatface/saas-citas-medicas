@extends('layouts.app')

@section('title', 'Mensajes')
@section('subtitle', 'Mensajería interna de la clínica')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="inline-flex bg-slate-100 rounded-lg p-1">
            <a href="{{ route('mensajes.index', ['box' => 'recibidos']) }}"
               class="px-4 py-1.5 rounded-md text-sm font-medium {{ $box === 'recibidos' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                Recibidos @if($noLeidos > 0)<span class="ml-1 px-1.5 py-0.5 rounded-full bg-rose-500 text-white text-[10px]">{{ $noLeidos }}</span>@endif
            </a>
            <a href="{{ route('mensajes.index', ['box' => 'enviados']) }}"
               class="px-4 py-1.5 rounded-md text-sm font-medium {{ $box === 'enviados' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                Enviados
            </a>
        </div>
        <a href="{{ route('mensajes.create') }}"
           class="sm:ml-auto inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Redactar
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden divide-y divide-slate-100">
        @forelse($mensajes as $m)
            @php $otro = $box === 'recibidos' ? $m->remitente : $m->destinatario; @endphp
            <a href="{{ route('mensajes.show', $m) }}" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 {{ $box === 'recibidos' && ! $m->leido ? 'bg-brand-50/40' : '' }}">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyanx-500 to-brand-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                    {{ $otro?->iniciales() ?? '?' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm {{ $box === 'recibidos' && ! $m->leido ? 'font-bold text-slate-900' : 'font-medium text-slate-700' }} truncate">{{ $otro?->name ?? 'Usuario' }}</p>
                        @if($box === 'recibidos' && ! $m->leido)<span class="w-2 h-2 rounded-full bg-brand-500 shrink-0"></span>@endif
                    </div>
                    <p class="text-sm text-slate-500 truncate">{{ $m->asunto }}</p>
                </div>
                <div class="text-xs text-slate-400 shrink-0">{{ $m->created_at?->diffForHumans() }}</div>
            </a>
        @empty
            <p class="px-5 py-12 text-center text-slate-400">No hay mensajes en esta bandeja.</p>
        @endforelse
    </div>

    <div>{{ $mensajes->links() }}</div>
</div>
@endsection
