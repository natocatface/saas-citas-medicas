@extends('layouts.app')

@section('title', 'Calendario')
@section('subtitle', 'Vista mensual de citas')

@php
    $estadoDot = ['pendiente' => '#f59e0b', 'confirmada' => '#0ea5e9', 'atendida' => '#10b981', 'cancelada' => '#f43f5e'];
    $estadoLbl = ['pendiente' => 'Pendientes', 'confirmada' => 'Confirmadas', 'atendida' => 'Atendidas', 'cancelada' => 'Canceladas'];
    $diasSemana = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
    // Conteo por estado del mes mostrado
    $resumen = ['pendiente' => 0, 'confirmada' => 0, 'atendida' => 0, 'cancelada' => 0];
    foreach ($semanas as $sem) {
        foreach ($sem as $d) {
            if ($d['enMes']) {
                foreach ($d['citas'] as $c) {
                    if (isset($resumen[$c->estado])) $resumen[$c->estado]++;
                }
            }
        }
    }
@endphp

@section('content')
<style>
@verbatim
.cal-wrap{width:100%;display:flex;flex-direction:column;gap:18px}
.cal-bar{background:#fff;border:1px solid #e6eaf0;border-radius:16px;box-shadow:0 1px 2px rgba(0,0,0,.04);padding:16px 20px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px}
.cal-title{font-size:22px;font-weight:800;color:#1e293b;text-transform:capitalize;line-height:1.1;margin:0}
.cal-subtxt{font-size:12px;color:#94a3b8;margin:3px 0 0}
.cal-tools{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.cal-nav{display:inline-flex;align-items:center;background:#f1f5f9;border-radius:10px;padding:3px;gap:2px}
.cal-nav a{display:flex;align-items:center;justify-content:center;height:34px;min-width:34px;padding:0 12px;border-radius:8px;color:#475569;font-size:14px;font-weight:600;text-decoration:none}
.cal-nav a:hover{background:#fff;color:#1e293b}
.cal-nav svg{width:18px;height:18px}
.cal-new{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(90deg,#17b8cf,#4f46e5);color:#fff;font-size:14px;font-weight:600;border-radius:10px;padding:0 16px;height:40px;text-decoration:none;box-shadow:0 3px 8px rgba(79,70,229,.25)}
.cal-new svg{width:16px;height:16px}
.cal-chips{display:flex;flex-wrap:wrap;gap:10px}
.cal-chip{display:inline-flex;align-items:center;gap:8px;font-size:13.5px;font-weight:600;color:#475569;background:#fff;border:1px solid #e6eaf0;border-radius:9999px;padding:7px 14px}
.cal-chip b{color:#1e293b}
.cal-dotlg{width:9px;height:9px;border-radius:9999px;flex:none}
.cal-card{background:#fff;border:1px solid #e6eaf0;border-radius:16px;box-shadow:0 1px 2px rgba(0,0,0,.04);overflow:hidden}
.cal-scroll{overflow-x:auto}
.cal-grid{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));min-width:840px}
.cal-dow{padding:13px 8px;text-align:center;font-size:12.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#64748b;background:#f8fafc;border-bottom:1px solid #e6eaf0;border-right:1px solid #eef2f6}
.cal-dow:last-child{border-right:0}
.cal-cell{min-height:148px;padding:8px;border-right:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;position:relative;transition:background .15s}
.cal-cell:nth-child(7n){border-right:0}
.cal-cell:hover{background:#fafcff}
.cal-cell.out{background:#fafbfc}
.cal-cell.wknd{background:#fbfdfe}
.cal-cell.is-today{background:#eef5ff;box-shadow:inset 0 0 0 2px #c7d2fe}
.cal-head{display:flex;align-items:center;justify-content:space-between;padding:0 3px 5px}
.cal-daynum{font-size:15px;font-weight:700;color:#334155}
.cal-daynum.dim{color:#cbd5e1}
.cal-today{display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:9999px;background:linear-gradient(135deg,#17b8cf,#4f46e5);color:#fff;font-size:14px;font-weight:700}
.cal-count{font-size:11.5px;font-weight:700;color:#64748b;background:#eef2f6;border-radius:9999px;padding:1px 8px}
.cal-ev{display:flex;align-items:center;gap:7px;padding:5px 8px;border-radius:7px;background:#f6f8fb;font-size:12.5px;line-height:1.3;text-decoration:none;margin-bottom:5px;border-left:3px solid #94a3b8}
.cal-ev:hover{background:#e9eef6}
.cal-ev .ti{font-weight:700;color:#334155;flex:none}
.cal-ev .nm{color:#475569;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cal-dot{width:8px;height:8px;border-radius:9999px;flex:none}
.cal-more{display:inline-block;font-size:12.5px;font-weight:600;color:#4f46e5;text-decoration:none;padding:3px 8px;border-radius:6px}
.cal-more:hover{background:#eef2ff}
.cal-legend{display:flex;flex-wrap:wrap;align-items:center;gap:16px;font-size:12px;color:#64748b;padding:2px}
@endverbatim
</style>

<div class="cal-wrap">

    {{-- Barra superior --}}
    <div class="cal-bar">
        <div>
            <p class="cal-title">{{ $ref->translatedFormat('F Y') }}</p>
            <p class="cal-subtxt">{{ $totalMes }} citas programadas este mes</p>
        </div>
        <div class="cal-tools">
            <div class="cal-nav">
                <a href="{{ route('calendario.index', ['mes' => $mesAnterior]) }}" title="Mes anterior">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <a href="{{ route('calendario.index') }}">Hoy</a>
                <a href="{{ route('calendario.index', ['mes' => $mesSiguiente]) }}" title="Mes siguiente">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </a>
            </div>
            <a href="{{ route('citas.create') }}" class="cal-new">
                <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nueva cita
            </a>
        </div>
    </div>

    {{-- Resumen por estado --}}
    <div class="cal-chips">
        @foreach($estadoLbl as $k => $label)
            <span class="cal-chip"><span class="cal-dotlg" style="background:{{ $estadoDot[$k] }}"></span>{{ $label }} <b>{{ $resumen[$k] }}</b></span>
        @endforeach
    </div>

    {{-- Cuadrícula --}}
    <div class="cal-card">
        <div class="cal-scroll">
            <div class="cal-grid">
                @foreach($diasSemana as $d)
                    <div class="cal-dow">{{ \Illuminate\Support\Str::substr($d, 0, 3) }}</div>
                @endforeach

                @foreach($semanas as $semana)
                    @foreach($semana as $dia)
                        @php
                            $esFinde = in_array($dia['fecha']->dayOfWeek, [0, 6]);
                            $cls = 'cal-cell';
                            if ($dia['hoy']) $cls .= ' is-today';
                            elseif (! $dia['enMes']) $cls .= ' out';
                            elseif ($esFinde) $cls .= ' wknd';
                        @endphp
                        <div class="{{ $cls }}">
                            <div class="cal-head">
                                @if($dia['hoy'])
                                    <span class="cal-today">{{ $dia['fecha']->format('j') }}</span>
                                @else
                                    <span class="cal-daynum {{ $dia['enMes'] ? '' : 'dim' }}">{{ $dia['fecha']->format('j') }}</span>
                                @endif
                                @if($dia['citas']->count() > 0)
                                    <span class="cal-count">{{ $dia['citas']->count() }}</span>
                                @endif
                            </div>
                            @foreach($dia['citas']->take(3) as $cita)
                                <a href="{{ route('citas.show', $cita) }}" class="cal-ev" title="{{ $cita->paciente->nombre_completo ?? '' }} · {{ $cita->medico->nombre_completo ?? '' }}"
                                   style="border-left-color: {{ $cita->medico->especialidad->color ?? '#94a3b8' }}">
                                    <span class="cal-dot" style="background:{{ $estadoDot[$cita->estado] ?? '#94a3b8' }}"></span>
                                    <span class="ti">{{ \Illuminate\Support\Str::of($cita->hora)->substr(0,5) }}</span>
                                    <span class="nm">{{ $cita->paciente->apellidos ?? '' }}</span>
                                </a>
                            @endforeach
                            @if($dia['citas']->count() > 3)
                                <a href="{{ route('citas.index', ['fecha' => $dia['fecha']->toDateString()]) }}" class="cal-more">+{{ $dia['citas']->count() - 3 }} más</a>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="cal-legend">
        <span style="font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;font-size:10px">Estado de la cita</span>
        @foreach($estadoLbl as $k => $label)
            <span style="display:inline-flex;align-items:center;gap:7px"><span class="cal-dotlg" style="background:{{ $estadoDot[$k] }}"></span>{{ $label }}</span>
        @endforeach
        <span style="margin-left:auto;color:#cbd5e1">La barra de color a la izquierda indica la especialidad</span>
    </div>
</div>
@endsection
