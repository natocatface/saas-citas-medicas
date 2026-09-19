<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    private function rango(Request $request): array
    {
        $desde = $request->date('desde') ?: Carbon::now()->startOfMonth()->subMonths(5)->startOfMonth();
        $hasta = $request->date('hasta') ?: Carbon::now()->endOfMonth();
        return [Carbon::parse($desde)->startOfDay(), Carbon::parse($hasta)->endOfDay()];
    }

    public function index(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);

        $base = fn () => Cita::whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()]);

        // Citas por mes (serie)
        $porMesRaw = $base()
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')->orderBy('ym')->pluck('total', 'ym');

        $serieLabels = [];
        $serieData = [];
        $cursor = $desde->copy()->startOfMonth();
        while ($cursor <= $hasta) {
            $ym = $cursor->format('Y-m');
            $serieLabels[] = $cursor->translatedFormat('M Y');
            $serieData[] = (int) ($porMesRaw[$ym] ?? 0);
            $cursor->addMonth();
        }

        // Por estado
        $porEstado = $base()->selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $estados = ['pendiente', 'confirmada', 'atendida', 'cancelada'];
        $estadoData = array_map(fn ($e) => (int) ($porEstado[$e] ?? 0), $estados);

        // Por especialidad (top 8)
        $porEsp = $base()
            ->join('medicos', 'citas.medico_id', '=', 'medicos.id')
            ->leftJoin('especialidades', 'medicos.especialidad_id', '=', 'especialidades.id')
            ->selectRaw("COALESCE(especialidades.nombre, 'Sin especialidad') as nombre, COALESCE(especialidades.color, '#94a3b8') as color, COUNT(*) as total")
            ->groupBy('nombre', 'color')->orderByDesc('total')->limit(8)->get();

        // Top médicos
        $topMedicos = Medico::withCount(['citas' => fn ($q) => $q->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])])
            ->orderByDesc('citas_count')->limit(8)->get();
        $maxMed = max(1, optional($topMedicos->first())->citas_count ?? 1);

        $totalRango = (int) array_sum($estadoData);

        return view('reportes.index', [
            'desde' => $desde->toDateString(),
            'hasta' => $hasta->toDateString(),
            'serieLabels' => $serieLabels,
            'serieData' => $serieData,
            'estadoData' => $estadoData,
            'porEsp' => $porEsp,
            'topMedicos' => $topMedicos,
            'maxMed' => $maxMed,
            'totalRango' => $totalRango,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$desde, $hasta] = $this->rango($request);

        $citas = Cita::with(['paciente', 'medico.especialidad'])
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->orderBy('fecha')->orderBy('hora')->get();

        $filename = 'reporte_citas_'.$desde->format('Ymd').'_'.$hasta->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($citas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 para Excel
            fputcsv($out, ['Fecha', 'Hora', 'Paciente', 'Medico', 'Especialidad', 'Estado', 'Motivo']);
            foreach ($citas as $c) {
                fputcsv($out, [
                    $c->fecha->format('Y-m-d'),
                    substr((string) $c->hora, 0, 5),
                    $c->paciente->nombre_completo ?? '',
                    $c->medico->nombre_completo ?? '',
                    $c->medico->especialidad->nombre ?? '',
                    ucfirst($c->estado),
                    $c->motivo ?? '',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
