@extends('layouts.app')

@section('title', 'Reportes')
@section('subtitle', 'Análisis de citas y rendimiento')

@section('content')
<div class="space-y-5">

    {{-- Filtros --}}
    <form method="GET" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4 flex flex-col sm:flex-row sm:items-end gap-3">
        <div>
            <label class="block text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1.5">Desde</label>
            <input type="date" name="desde" value="{{ $desde }}" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-[11px] font-semibold tracking-wider text-slate-400 uppercase mb-1.5">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta }}" class="rounded-lg border-slate-300 text-sm focus:border-brand-500 focus:ring-brand-500">
        </div>
        <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg px-5 py-2.5 shadow-sm">Aplicar</button>
        <a href="{{ route('reportes.export', ['desde' => $desde, 'hasta' => $hasta]) }}"
           class="inline-flex items-center gap-2 border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold rounded-lg px-5 py-2.5 sm:ml-auto">
            @include('partials.icon', ['name' => 'document', 'class' => 'w-4 h-4']) Exportar CSV
        </a>
    </form>

    {{-- Total del rango --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-brand-50 to-white border border-slate-200/70 rounded-2xl shadow-sm p-5">
            <p class="text-[11px] font-semibold tracking-wider text-slate-500">TOTAL DE CITAS</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ number_format($totalRango) }}</p>
            <p class="text-xs text-slate-400 mt-1">en el rango seleccionado</p>
        </div>
        @php $estLabels = ['Pendientes','Confirmadas','Atendidas','Canceladas']; $estColors = ['text-amber-600','text-sky-600','text-emerald-600','text-rose-600']; @endphp
        @foreach($estadoData as $i => $val)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
                <p class="text-[11px] font-semibold tracking-wider text-slate-400">{{ strtoupper($estLabels[$i]) }}</p>
                <p class="text-3xl font-bold {{ $estColors[$i] }} mt-2">{{ number_format($val) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Gráficos principales --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-slate-800">Citas por mes</h3>
            <p class="text-xs text-slate-400">Evolución en el período</p>
            <div class="h-64 mt-4"><canvas id="chartMes"></canvas></div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-slate-800">Por estado</h3>
            <p class="text-xs text-slate-400">Distribución</p>
            <div class="h-64 mt-4"><canvas id="chartEstado"></canvas></div>
        </div>
    </div>

    {{-- Especialidad + Top médicos --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-slate-800 mb-4">Citas por especialidad</h3>
            @php $maxEsp = max(1, $porEsp->max('total') ?? 1); @endphp
            <div class="space-y-3">
                @forelse($porEsp as $e)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="inline-flex items-center gap-2 text-sm text-slate-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $e->color }}"></span>{{ $e->nombre }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500">{{ $e->total }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full" style="width: {{ max(4, round(($e->total / $maxEsp) * 100)) }}%; background: {{ $e->color }}"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 py-6 text-center">Sin datos en el período.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
            <h3 class="font-bold text-slate-800 mb-4">Top médicos</h3>
            <div class="space-y-3">
                @forelse($topMedicos as $i => $m)
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-brand-50 text-brand-600 text-xs font-bold flex items-center justify-center shrink-0">{{ $i+1 }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-medium text-slate-700 truncate">{{ $m->nombre_completo }}</p>
                                <p class="text-xs font-semibold text-slate-500 shrink-0 ml-2">{{ $m->citas_count }}</p>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-brand-400" style="width: {{ max(4, round(($m->citas_count / $maxMed) * 100)) }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-400 py-6 text-center">Sin datos.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    const R_MES_LABELS = @json($serieLabels);
    const R_MES_DATA   = @json($serieData);
    const R_ESTADO     = @json($estadoData);
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new Chart(document.getElementById('chartMes'), {
        type: 'line',
        data: { labels: R_MES_LABELS, datasets: [{
            data: R_MES_DATA, borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.12)',
            fill: true, tension: 0.35, pointBackgroundColor: '#6366f1', pointRadius: 4,
        }]},
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor:'#1e293b' } },
            scales: {
                x: { grid: { display: false }, ticks: { color:'#94a3b8', font:{size:11} } },
                y: { beginAtZero: true, ticks: { precision:0, color:'#cbd5e1', font:{size:11} }, grid: { color:'#f1f5f9' }, border:{display:false} }
            }
        }
    });

    new Chart(document.getElementById('chartEstado'), {
        type: 'doughnut',
        data: { labels: ['Pendiente','Confirmada','Atendida','Cancelada'], datasets: [{
            data: R_ESTADO, backgroundColor: ['#f59e0b','#0ea5e9','#10b981','#f43f5e'], borderWidth: 0, cutout: '65%',
        }]},
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position:'bottom', labels:{ boxWidth:8, boxHeight:8, usePointStyle:true, padding:12, color:'#64748b', font:{size:11} } }, tooltip:{backgroundColor:'#1e293b'} }
        }
    });
});
</script>
@endsection
