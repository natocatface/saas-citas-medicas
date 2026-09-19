@extends('layouts.app')

@section('title', 'Panel SaaS')
@section('subtitle', 'Visión general de la plataforma')

@php
    $cards = [
        ['CLÍNICAS', number_format($clinicas), 'building', 'from-cyan-50', 'bg-cyan-100 text-cyan-600', 'bg-cyan-400'],
        ['SUSCRIPCIONES ACTIVAS', number_format($activas), 'shield', 'from-emerald-50', 'bg-emerald-100 text-emerald-600', 'bg-emerald-400'],
        ['INGRESOS / MES', 'S/ '.number_format($ingresos, 0), 'cash', 'from-violet-50', 'bg-violet-100 text-violet-600', 'bg-violet-400'],
        ['USUARIOS', number_format($totalUsuarios), 'users', 'from-indigo-50', 'bg-indigo-100 text-indigo-600', 'bg-indigo-400'],
    ];
@endphp

@section('content')
<div class="space-y-6">

    {{-- Tarjetas principales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($cards as $c)
            <div class="relative bg-gradient-to-br {{ $c[3] }} to-white rounded-2xl border border-slate-200/70 p-5 pl-6 flex items-start justify-between shadow-sm overflow-hidden">
                <span class="absolute left-0 top-0 h-full w-1.5 {{ $c[5] }}"></span>
                <div>
                    <p class="text-[11px] font-semibold tracking-wider text-slate-500">{{ $c[0] }}</p>
                    <p class="text-3xl font-bold text-slate-800 mt-2">{{ $c[1] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl {{ $c[4] }} flex items-center justify-center shadow-sm">
                    @include('partials.icon', ['name' => $c[2], 'class' => 'w-5 h-5'])
                </div>
            </div>
        @endforeach
    </div>

    {{-- Estado de suscripciones --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
        @foreach([
            ['En prueba', $prueba, 'text-amber-600'],
            ['Suspendidas', $suspendidas, 'text-rose-600'],
            ['Pacientes', number_format($totalPacientes), 'text-emerald-600'],
            ['Médicos', number_format($totalMedicos), 'text-fuchsia-600'],
            ['Citas totales', number_format($totalCitas), 'text-sky-600'],
            ['Tasa activas', $clinicas ? round($activas/$clinicas*100).'%' : '0%', 'text-cyan-600'],
        ] as $s)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
                <p class="text-2xl font-bold {{ $s[2] }}">{{ $s[1] }}</p>
                <p class="text-[11px] font-semibold tracking-wide text-slate-400 mt-1">{{ strtoupper($s[0]) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Gráfico + distribución por plan --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-slate-800">Nuevas clínicas por mes</h3>
            <p class="text-xs text-slate-400">Altas en los últimos 6 meses</p>
            <div class="h-64 mt-4"><canvas id="chartClinicas"></canvas></div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-slate-800 mb-4">Clínicas por plan</h3>
            @php $maxPlan = max(1, optional($porPlan->sortByDesc('clinicas_count')->first())->clinicas_count ?? 1); @endphp
            <div class="space-y-3">
                @forelse($porPlan as $pl)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-slate-600">{{ $pl->nombre }} <span class="text-slate-400">· S/ {{ number_format($pl->precio, 0) }}</span></span>
                            <span class="text-xs font-semibold text-slate-500">{{ $pl->clinicas_count }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-cyanx-500 to-brand-600" style="width: {{ max(4, round(($pl->clinicas_count / $maxPlan) * 100)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Sin planes.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Clínicas recientes + Bitácora --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-5 pb-3">
                <h3 class="font-bold text-slate-800">Clínicas recientes</h3>
                <a href="{{ route('saas.clinicas.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Ver todas</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($clinicasRecientes as $cl)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center text-white shrink-0" style="background: {{ $cl->color }}">
                            @include('partials.icon', ['name' => 'building', 'class' => 'w-5 h-5'])
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-slate-800 truncate">{{ $cl->nombre }}</p>
                            <p class="text-xs text-slate-400">{{ $cl->plan->nombre ?? 'Sin plan' }}</p>
                        </div>
                        @include('saas.partials.estado-suscripcion', ['estado' => $cl->estado_suscripcion])
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-slate-400">Sin clínicas.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between p-5 pb-3">
                <h3 class="font-bold text-slate-800">Actividad reciente</h3>
                <a href="{{ route('saas.bitacora.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Ver bitácora</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($bitacora as $b)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <span class="w-2 h-2 rounded-full bg-cyanx-500 shrink-0"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-slate-700 truncate"><span class="font-medium">{{ $b->usuario_nombre ?? 'Sistema' }}</span> · {{ $b->accion }}</p>
                            <p class="text-xs text-slate-400">{{ $b->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-slate-400">Sin actividad registrada.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    const SA_LABELS = @json($labels);
    const SA_DATA = @json($valores);
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('chartClinicas'), {
        type: 'bar',
        data: { labels: SA_LABELS, datasets: [{ data: SA_DATA, backgroundColor: '#17b8cf', hoverBackgroundColor: '#0e7490', borderRadius: 8, maxBarThickness: 46 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b' } },
            scales: { x: { grid: { display: false }, ticks: { color:'#94a3b8', font:{size:11} } },
                      y: { beginAtZero: true, ticks: { precision:0, color:'#cbd5e1', font:{size:11} }, grid:{color:'#f1f5f9'}, border:{display:false} } }
        }
    });
});
</script>
@endsection
