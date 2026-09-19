<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Cita;
use App\Models\Factura;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MantenimientoController extends Controller
{
    public function index()
    {
        $info = [
            'Aplicación' => config('app.name', 'CitasMédicas'),
            'Entorno' => config('app.env', 'local'),
            'Versión PHP' => PHP_VERSION,
            'Versión Laravel' => app()->version(),
            'Base de datos' => $this->dbStatus(),
            'Zona horaria' => config('app.timezone'),
            'Fecha del servidor' => now()->translatedFormat('d M Y, H:i'),
        ];

        $conteos = [
            ['Pacientes', Paciente::count(), 'users', '#10b981'],
            ['Médicos', Medico::count(), 'stethoscope', '#d946ef'],
            ['Citas', Cita::count(), 'calendar', '#0ea5e9'],
            ['Facturas', Factura::count(), 'cash', '#f59e0b'],
            ['Recetas', Receta::count(), 'document', '#6366f1'],
            ['Usuarios', User::where('clinica_id', auth()->user()->clinica_id)->count(), 'user-circle', '#17b8cf'],
        ];

        $bitacoraAntigua = Bitacora::where('clinica_id', auth()->user()->clinica_id)
            ->where('created_at', '<', now()->subDays(90))->count();

        return view('mantenimiento.index', compact('info', 'conteos', 'bitacoraAntigua'));
    }

    private function dbStatus(): string
    {
        try {
            DB::connection()->getPdo();
            return 'Conectada · '.DB::connection()->getDatabaseName();
        } catch (\Throwable $e) {
            return 'Sin conexión';
        }
    }

    public function limpiarCache()
    {
        Artisan::call('optimize:clear');
        return back()->with('success', 'Caché del sistema limpiada (vistas, rutas y configuración).');
    }

    public function limpiarBitacora()
    {
        $dias = 90;
        $n = Bitacora::where('clinica_id', auth()->user()->clinica_id)
            ->where('created_at', '<', now()->subDays($dias))->delete();
        return back()->with('success', "Se eliminaron {$n} registro(s) de bitácora con más de {$dias} días.");
    }

    public function respaldo(): StreamedResponse
    {
        $data = [
            'generado_en' => now()->toIso8601String(),
            'clinica_id' => auth()->user()->clinica_id,
            'pacientes' => Paciente::get()->toArray(),
            'medicos' => Medico::with('especialidad')->get()->toArray(),
            'citas' => Cita::get()->toArray(),
            'facturas' => Factura::with('items')->get()->toArray(),
            'recetas' => Receta::with('items')->get()->toArray(),
        ];
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'respaldo_clinica_'.now()->format('Ymd_His').'.json';

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, $filename, ['Content-Type' => 'application/json; charset=UTF-8']);
    }
}
