<?php

namespace App\Http\Controllers\SaaS;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Clinica;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $clinicas = Clinica::count();
        $activas = Clinica::where('estado_suscripcion', 'activa')->count();
        $prueba = Clinica::where('estado_suscripcion', 'prueba')->count();
        $suspendidas = Clinica::whereIn('estado_suscripcion', ['suspendida', 'cancelada'])->count();

        // Ingresos mensuales estimados (clínicas activas × precio de su plan)
        $ingresos = (float) Clinica::where('estado_suscripcion', 'activa')
            ->join('planes', 'clinicas.plan_id', '=', 'planes.id')
            ->sum('planes.precio');

        // Métricas globales (superadmin no tiene clínica → scope desactivado, ve todo)
        $totalUsuarios = User::count();
        $totalPacientes = Paciente::count();
        $totalMedicos = Medico::count();
        $totalCitas = Cita::count();

        // Nuevas clínicas por mes (últimos 6 meses)
        $labels = [];
        $valores = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $labels[] = $mes->translatedFormat('M Y');
            $valores[] = Clinica::whereYear('created_at', $mes->year)->whereMonth('created_at', $mes->month)->count();
        }

        // Distribución por plan
        $porPlan = Plan::withCount('clinicas')->orderBy('precio')->get();

        $clinicasRecientes = Clinica::with('plan')->latest()->limit(6)->get();
        $bitacora = Bitacora::latest('created_at')->limit(8)->get();

        return view('saas.dashboard', compact(
            'clinicas', 'activas', 'prueba', 'suspendidas', 'ingresos',
            'totalUsuarios', 'totalPacientes', 'totalMedicos', 'totalCitas',
            'labels', 'valores', 'porPlan', 'clinicasRecientes', 'bitacora'
        ));
    }
}
