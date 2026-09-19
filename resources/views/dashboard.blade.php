@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen general de la clínica')

@section('content')
{{-- CSS propio de esta página (inline, no depende de ningún archivo externo ni CDN) --}}
<style>
@verbatim
.dz{max-width:1500px}
.dz *{box-sizing:border-box}
.dz-grid4{display:grid;grid-template-columns:repeat(1,1fr);gap:16px}
.dz-grid2{display:grid;grid-template-columns:repeat(1,1fr);gap:16px}
@media(min-width:640px){.dz-grid4{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1100px){.dz-grid4{grid-template-columns:repeat(4,1fr)}.dz-grid2{grid-template-columns:repeat(2,1fr)}}
.dz-card{background:#fff;border:1px solid #e6eaf0;border-radius:16px;box-shadow:0 1px 2px rgba(0,0,0,.04);padding:20px}
.dz-stat{position:relative;display:flex;align-items:flex-start;justify-content:space-between;overflow:hidden}
.dz-stat .bar{position:absolute;left:0;top:0;height:100%;width:6px}
.dz-stat .lbl{font-size:11px;font-weight:600;letter-spacing:.06em;color:#64748b}
.dz-stat .val{font-size:30px;font-weight:800;color:#1e293b;margin-top:8px}
.dz-stat .chip{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center}
.dz-stat .chip svg{width:22px;height:22px}
.dz-stt{display:flex;align-items:center;gap:12px}
.dz-stt .ic{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center}
.dz-stt .ic svg{width:20px;height:20px}
.dz-stt .n{font-size:24px;font-weight:800;color:#1e293b;line-height:1}
.dz-stt .t{font-size:11px;font-weight:600;letter-spacing:.04em;color:#64748b;margin-top:4px}
.dz-h3{font-size:16px;font-weight:700;color:#1e293b;margin:0}
.dz-sub{font-size:12px;color:#94a3b8;margin:2px 0 0}
.dz-row{display:flex;align-items:center;gap:14px;padding:12px 0;border-top:1px solid #f1f5f9}
.dz-row:first-child{border-top:0}
.dz-badge{display:inline-block;padding:3px 9px;border-radius:9999px;font-size:11px;font-weight:600}
.dz-bar-bg{height:8px;border-radius:9999px;background:#eef2f6;overflow:hidden}
.dz-bar-fill{height:100%;border-radius:9999px;background:linear-gradient(90deg,#f59e0b,#fbbf24)}
.dz-link{font-size:12px;font-weight:600;color:#4f46e5;text-decoration:none}
.dz-mut{color:#94a3b8}
.dz-charts2{display:grid;grid-template-columns:repeat(1,1fr);gap:16px}
@media(min-width:1024px){.dz-charts2{grid-template-columns:repeat(2,1fr)}}
@endverbatim
</style>

@php
    $tarjetas = [
        ['CITAS HOY', number_format($citasHoy), 'calendar', '#06b6d4', '#ecfeff', '#cffafe', '#0891b2'],
        ['ESTA SEMANA', number_format($citasSemana), 'calendar-days', '#6366f1', '#eef2ff', '#e0e7ff', '#4f46e5'],
        ['PACIENTES', number_format($totalPacientes), 'users', '#10b981', '#ecfdf5', '#d1fae5', '#059669'],
        ['MÉDICOS', number_format($totalMedicos), 'stethoscope', '#d946ef', '#fdf4ff', '#fae8ff', '#c026d3'],
    ];
    $estados = [
        ['PENDIENTES', $pendientes, 'clock', '#f59e0b', '#fffbeb', '#d97706'],
        ['CONFIRMADAS', $confirmadas, 'calendar', '#0ea5e9', '#f0f9ff', '#0284c7'],
        ['ATENDIDAS', $atendidas, 'shield', '#10b981', '#ecfdf5', '#059669'],
        ['CANCELADAS', $canceladas, 'logout', '#f43f5e', '#fff1f2', '#e11d48'],
    ];
    $estadoColor = ['pendiente'=>['#fef3c7','#b45309'],'confirmada'=>['#e0f2fe','#0369a1'],'atendida'=>['#d1fae5','#047857'],'cancelada'=>['#ffe4e6','#be123c']];
@endphp

<div class="dz" style="display:flex;flex-direction:column;gap:20px">

    {{-- Tarjetas principales --}}
    <div class="dz-grid4">
        @foreach($tarjetas as $c)
            <div class="dz-card dz-stat" style="padding-left:26px">
                <span class="bar" style="background:{{ $c[3] }}"></span>
                <div>
                    <p class="lbl" style="margin:0">{{ $c[0] }}</p>
                    <p class="val" style="margin:0">{{ $c[1] }}</p>
                </div>
                <div class="chip" style="background:{{ $c[5] }};color:{{ $c[6] }}">
                    @include('partials.icon', ['name' => $c[2], 'class' => ''])
                </div>
            </div>
        @endforeach
    </div>

    {{-- Estado de las citas --}}
    <div class="dz-grid4">
        @foreach($estados as $e)
            <div class="dz-card dz-stt" style="border-left:6px solid {{ $e[3] }}">
                <div class="ic" style="background:{{ $e[4] }};color:{{ $e[5] }}">
                    @include('partials.icon', ['name' => $e[2], 'class' => ''])
                </div>
                <div>
                    <div class="n">{{ $e[1] }}</div>
                    <div class="t">{{ $e[0] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráficos (4 paneles responsive en cuadrícula 2x2) --}}
    <div class="dz-charts2">
        {{-- 1. Actividad últimos 7 días (área) --}}
        <div class="dz-card">
            <p class="dz-h3">Actividad de Citas</p>
            <p class="dz-sub">Volumen de citas de los últimos 7 días</p>
            <div style="height:260px;margin-top:14px"><canvas id="dzArea"></canvas></div>
        </div>

        {{-- 2. Estado general (dona) --}}
        <div class="dz-card">
            <p class="dz-h3">Estado General</p>
            <p class="dz-sub">Distribución de todas las citas</p>
            <div style="height:260px;margin-top:14px;position:relative">
                <canvas id="dzDonut"></canvas>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;padding-bottom:40px">
                    <span style="font-size:12px;color:#94a3b8">Total</span>
                    <span style="font-size:30px;font-weight:800;color:#1e293b">{{ $totalCitas }}</span>
                </div>
            </div>
        </div>

        {{-- 3. Citas por especialidad (barras horizontales) --}}
        <div class="dz-card">
            <p class="dz-h3">Citas por Especialidad</p>
            <p class="dz-sub">Especialidades con mayor número de citas</p>
            <div style="height:260px;margin-top:14px">
                @if(count($espLabels))
                    <canvas id="dzEsp"></canvas>
                @else
                    <p class="dz-mut" style="text-align:center;padding:60px 0;font-size:14px">Sin datos de especialidades.</p>
                @endif
            </div>
        </div>

        {{-- 4. Tendencia mensual (línea) --}}
        <div class="dz-card">
            <p class="dz-h3">Tendencia Mensual</p>
            <p class="dz-sub">Evolución de citas en los últimos 6 meses</p>
            <div style="height:260px;margin-top:14px"><canvas id="dzTrend"></canvas></div>
        </div>
    </div>

    {{-- Agenda + Rendimiento --}}
    <div class="dz-grid2">
        <div class="dz-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <div><p class="dz-h3">Agenda Próxima</p><p class="dz-sub">Pacientes agendados próximamente</p></div>
                <a class="dz-link" href="{{ route('calendario.index') }}">Ver Calendario</a>
            </div>
            @forelse($agenda as $cita)
                @php $ec = $estadoColor[$cita->estado] ?? ['#f1f5f9','#475569']; @endphp
                <div class="dz-row">
                    <div style="width:46px;text-align:center;flex:none">
                        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase">{{ $cita->fecha->translatedFormat('M') }}</div>
                        <div style="font-size:18px;font-weight:800;color:#334155;line-height:1">{{ $cita->fecha->format('d') }}</div>
                    </div>
                    <div style="min-width:0;flex:1">
                        <div style="font-size:14px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $cita->paciente->nombre_completo ?? 'Paciente' }}</div>
                        <div style="font-size:12px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $cita->medico->nombre_completo ?? '' }} · {{ $cita->medico->especialidad->nombre ?? 'General' }}</div>
                    </div>
                    <div style="text-align:right;flex:none">
                        <div style="font-size:14px;font-weight:600;color:#475569">{{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }}</div>
                        <span class="dz-badge" style="background:{{ $ec[0] }};color:{{ $ec[1] }};margin-top:4px">{{ ucfirst($cita->estado) }}</span>
                    </div>
                </div>
            @empty
                <p class="dz-mut" style="text-align:center;padding:24px 0;font-size:14px">No hay citas próximas.</p>
            @endforelse
        </div>

        <div class="dz-card">
            <p class="dz-h3">Rendimiento Médico</p>
            <p class="dz-sub" style="margin-bottom:14px">Top 5 médicos con más citas (mes actual)</p>
            @forelse($topMedicos as $i => $m)
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                    <div style="width:28px;height:28px;border-radius:9999px;background:#eef2ff;color:#4f46e5;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex:none">{{ $i+1 }}</div>
                    <div style="min-width:0;flex:1">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
                            <span style="font-size:14px;color:#334155;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $m->nombre_completo }}</span>
                            <span style="font-size:12px;font-weight:600;color:#64748b;flex:none;margin-left:8px">{{ $m->citas_count }} citas</span>
                        </div>
                        <div class="dz-bar-bg"><div class="dz-bar-fill" style="width:{{ max(6, round(($m->citas_count / $maxCitas) * 100)) }}%"></div></div>
                    </div>
                </div>
            @empty
                <p class="dz-mut" style="text-align:center;padding:24px 0;font-size:14px">Sin datos de rendimiento.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    const DZ_LABELS = @json($labels);
    const DZ_DATA   = @json($valores);
    const DZ_DONUT  = @json($donut);
    const DZ_ESP_L  = @json($espLabels);
    const DZ_ESP_D  = @json($espData);
    const DZ_TR_L   = @json($trendLabels);
    const DZ_TR_D   = @json($trendData);
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.font.family = "'Inter','Segoe UI',system-ui,sans-serif";
    Chart.defaults.color = '#94a3b8';

    // Utilidad: gradiente vertical para áreas
    const vGrad = (ctx, c1, c2) => {
        const a = ctx.chart.chartArea;
        if (!a) return c1;
        const g = ctx.chart.ctx.createLinearGradient(0, a.top, 0, a.bottom);
        g.addColorStop(0, c1); g.addColorStop(1, c2);
        return g;
    };

    const tip = {
        backgroundColor:'#0f172a', titleColor:'#fff', bodyColor:'#e2e8f0',
        padding:10, cornerRadius:8, displayColors:false,
        titleFont:{weight:'600',size:12}, bodyFont:{size:13}
    };

    // 1. Actividad últimos 7 días (área suavizada)
    new Chart(document.getElementById('dzArea'), {
        type: 'line',
        data: { labels: DZ_LABELS, datasets: [{
            data: DZ_DATA, borderColor: '#6366f1', borderWidth: 3,
            fill: true, tension: 0.4,
            pointBackgroundColor:'#fff', pointBorderColor:'#6366f1',
            pointBorderWidth:2, pointRadius:4, pointHoverRadius:6,
            backgroundColor: (c)=>vGrad(c,'rgba(99,102,241,.28)','rgba(99,102,241,0)')
        }] },
        options: { responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{display:false}, tooltip:tip },
            scales:{ x:{grid:{display:false},ticks:{color:'#94a3b8'}},
                     y:{beginAtZero:true,ticks:{precision:0,color:'#cbd5e1'},grid:{color:'#f1f5f9'}} } }
    });

    // 2. Estado general (dona)
    new Chart(document.getElementById('dzDonut'), {
        type:'doughnut',
        data:{ labels:['Confirmada','Pendiente','Completada','Cancelada'],
            datasets:[{ data:[DZ_DONUT.confirmada,DZ_DONUT.pendiente,DZ_DONUT.completada,DZ_DONUT.cancelada],
                backgroundColor:['#f59e0b','#3b82f6','#10b981','#6366f1'],
                borderWidth:3, borderColor:'#fff', hoverOffset:8, cutout:'72%' }] },
        options:{ responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{position:'bottom',labels:{boxWidth:8,usePointStyle:true,padding:14,color:'#64748b'}}, tooltip:tip } }
    });

    // 3. Citas por especialidad (barras horizontales)
    const espEl = document.getElementById('dzEsp');
    if (espEl) new Chart(espEl, {
        type:'bar',
        data:{ labels:DZ_ESP_L, datasets:[{
            data:DZ_ESP_D, borderRadius:6, maxBarThickness:26,
            backgroundColor:(c)=>vGrad(c,'#22d3ee','#0891b2')
        }] },
        options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{display:false}, tooltip:tip },
            scales:{ x:{beginAtZero:true,ticks:{precision:0,color:'#cbd5e1'},grid:{color:'#f1f5f9'}},
                     y:{grid:{display:false},ticks:{color:'#475569',font:{weight:'600'}}} } }
    });

    // 4. Tendencia mensual (línea/área)
    new Chart(document.getElementById('dzTrend'), {
        type:'line',
        data:{ labels:DZ_TR_L, datasets:[{
            data:DZ_TR_D, borderColor:'#10b981', borderWidth:3,
            fill:true, tension:0.4,
            pointBackgroundColor:'#fff', pointBorderColor:'#10b981',
            pointBorderWidth:2, pointRadius:4, pointHoverRadius:6,
            backgroundColor:(c)=>vGrad(c,'rgba(16,185,129,.25)','rgba(16,185,129,0)')
        }] },
        options:{ responsive:true, maintainAspectRatio:false,
            plugins:{ legend:{display:false}, tooltip:tip },
            scales:{ x:{grid:{display:false},ticks:{color:'#94a3b8'}},
                     y:{beginAtZero:true,ticks:{precision:0,color:'#cbd5e1'},grid:{color:'#f1f5f9'}} } }
    });
});
</script>
@endsection
