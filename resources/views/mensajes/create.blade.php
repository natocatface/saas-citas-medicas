@extends('layouts.app')

@section('title', 'Redactar mensaje')
@section('subtitle', 'Enviar a un miembro de la clínica')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
        <form method="POST" action="{{ route('mensajes.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Para <span class="text-rose-500">*</span></label>
                <select name="destinatario_id" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">— Seleccionar destinatario —</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}" @selected(old('destinatario_id') == $u->id)>{{ $u->name }} ({{ ucfirst($u->rol) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Asunto <span class="text-rose-500">*</span></label>
                <input type="text" name="asunto" value="{{ old('asunto') }}" required
                       class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Mensaje <span class="text-rose-500">*</span></label>
                <textarea name="cuerpo" rows="6" required class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">{{ old('cuerpo') }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Enviar mensaje</button>
                <a href="{{ route('mensajes.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
