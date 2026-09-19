@extends('layouts.app')

@section('title', 'Usuarios globales')
@section('subtitle', 'Todas las cuentas de la plataforma')

@php
    $rolBadge = [
        'superadmin' => 'bg-cyan-100 text-cyan-700',
        'admin'      => 'bg-violet-100 text-violet-700',
        'medico'     => 'bg-sky-100 text-sky-700',
        'recepcion'  => 'bg-amber-100 text-amber-700',
    ];
@endphp

@section('content')
<div class="space-y-5">

    <div class="flex flex-col lg:flex-row lg:items-center gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 flex-1">
            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-lg px-3 py-2 flex-1 max-w-sm">
                <span class="text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar usuario..."
                       class="bg-transparent border-0 p-0 text-sm focus:ring-0 w-full placeholder:text-slate-400">
            </div>
            <select name="rol" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-40">
                <option value="">Todos los roles</option>
                @foreach($roles as $k => $label)
                    <option value="{{ $k }}" @selected($rolSel === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="clinica_id" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500 sm:w-48">
                <option value="">Todas las clínicas</option>
                @foreach($clinicas as $cl)
                    <option value="{{ $cl->id }}" @selected($clinicaSel == $cl->id)>{{ $cl->nombre }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('saas.usuarios.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition shadow-sm shrink-0">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Nuevo usuario
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-semibold tracking-wider text-slate-400 uppercase bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-3">Usuario</th>
                        <th class="px-5 py-3">Rol</th>
                        <th class="px-5 py-3">Clínica</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($usuarios as $u)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyanx-500 to-brand-600 text-white text-xs font-bold flex items-center justify-center shrink-0">{{ $u->iniciales() }}</div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $u->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $rolBadge[$u->rol] ?? 'bg-slate-100 text-slate-600' }}">{{ $roles[$u->rol] ?? ucfirst($u->rol) }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $u->clinica->nombre ?? '— (plataforma)' }}</td>
                            <td class="px-5 py-3 text-center">@include('partials.badge-estado', ['activo' => $u->activo])</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('saas.usuarios.edit', $u) }}" class="px-2.5 py-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-brand-600 text-xs font-medium">Editar</a>
                                    @if($u->id !== auth()->id())
                                        @include('partials.boton-eliminar', ['action' => route('saas.usuarios.destroy', $u), 'nombre' => $u->name])
                                    @else
                                        <span class="px-2.5 py-1.5 text-xs text-slate-300">Tú</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No hay usuarios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $usuarios->links() }}</div>
</div>
@endsection
