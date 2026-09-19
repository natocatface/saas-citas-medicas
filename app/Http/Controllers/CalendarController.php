<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Mes a mostrar (YYYY-MM); por defecto el actual
        try {
            $ref = $request->filled('mes')
                ? Carbon::createFromFormat('Y-m', $request->input('mes'))->startOfMonth()
                : Carbon::now()->startOfMonth();
        } catch (\Throwable $e) {
            $ref = Carbon::now()->startOfMonth();
        }

        $inicioMes = $ref->copy()->startOfMonth();
        $finMes = $ref->copy()->endOfMonth();

        // Rango de la cuadrícula (semana inicia lunes)
        $inicioGrid = $inicioMes->copy()->startOfWeek(Carbon::MONDAY);
        $finGrid = $finMes->copy()->endOfWeek(Carbon::SUNDAY);

        // Citas del rango, agrupadas por fecha (Y-m-d)
        $citas = Cita::with(['paciente', 'medico.especialidad'])
            ->whereBetween('fecha', [$inicioGrid->toDateString(), $finGrid->toDateString()])
            ->orderBy('hora')
            ->get()
            ->groupBy(fn ($c) => $c->fecha->toDateString());

        // Construir semanas
        $dias = [];
        $cursor = $inicioGrid->copy();
        while ($cursor <= $finGrid) {
            $key = $cursor->toDateString();
            $dias[] = [
                'fecha' => $cursor->copy(),
                'enMes' => $cursor->month === $ref->month,
                'hoy' => $cursor->isToday(),
                'citas' => $citas->get($key, collect()),
            ];
            $cursor->addDay();
        }
        $semanas = array_chunk($dias, 7);

        return view('calendario.index', [
            'ref' => $ref,
            'semanas' => $semanas,
            'mesAnterior' => $ref->copy()->subMonth()->format('Y-m'),
            'mesSiguiente' => $ref->copy()->addMonth()->format('Y-m'),
            'totalMes' => $citas->flatten()->where('fecha.month', $ref->month)->count(),
        ]);
    }
}
