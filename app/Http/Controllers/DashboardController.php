<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()?->rol === 'superadmin') {
            return redirect()->route('saas.dashboard');
        }

        $hoy = Carbon::today();
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();

        // Tarjetas superiores
        $citasHoy = Cita::whereDate('fecha', $hoy)->count();
        $citasSemana = Cita::whereBetween('fecha', [$inicioSemana, $finSemana])->count();
        $totalPacientes = Paciente::count();
        $totalMedicos = Medico::count();

        // Tarjetas de estado
        $pendientes = Cita::where('estado', 'pendiente')->count();
        $confirmadas = Cita::where('estado', 'confirmada')->count();
        $atendidas = Cita::where('estado', 'atendida')->count();
        $canceladas = Cita::where('estado', 'cancelada')->count();
        $totalCitas = Cita::count();

        // Grafico de barras: ultimos 7 dias
        $labels = [];
        $valores = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::today()->subDays($i);
            $labels[] = $dia->translatedFormat('d M');
            $valores[] = Cita::whereDate('fecha', $dia)->count();
        }

        // Donut: distribucion por estado
        $donut = [
            'confirmada' => $confirmadas,
            'pendiente'  => $pendientes,
            'completada' => $atendidas,
            'cancelada'  => $canceladas,
        ];

        // Grafico: citas por especialidad (top 6)
        $espRows = Cita::query()
            ->join('medicos', 'citas.medico_id', '=', 'medicos.id')
            ->join('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->select('especialidades.nombre', DB::raw('COUNT(citas.id) as total'))
            ->groupBy('especialidades.nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();
        $espLabels = $espRows->pluck('nombre');
        $espData   = $espRows->pluck('total');

        // Grafico: tendencia de citas en los ultimos 6 meses
        $trendLabels = [];
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $trendLabels[] = ucfirst($mes->translatedFormat('M'));
            $trendData[] = Cita::whereMonth('fecha', $mes->month)
                ->whereYear('fecha', $mes->year)
                ->count();
        }

        // Agenda proxima
        $agenda = Cita::with(['paciente', 'medico.especialidad'])
            ->whereDate('fecha', '>=', $hoy)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->limit(6)
            ->get();

        // Rendimiento medico: top 5 del mes
        $topMedicos = Medico::withCount(['citas' => function ($q) {
                $q->whereMonth('fecha', Carbon::now()->month)
                  ->whereYear('fecha', Carbon::now()->year);
            }])
            ->orderByDesc('citas_count')
            ->limit(5)
            ->get();
        $maxCitas = max(1, optional($topMedicos->first())->citas_count ?? 1);

        return view('dashboard', compact(
            'citasHoy', 'citasSemana', 'totalPacientes', 'totalMedicos',
            'pendientes', 'confirmadas', 'atendidas', 'canceladas', 'totalCitas',
            'labels', 'valores', 'donut', 'agenda', 'topMedicos', 'maxCitas',
            'espLabels', 'espData', 'trendLabels', 'trendData'
        ));
    }
}
