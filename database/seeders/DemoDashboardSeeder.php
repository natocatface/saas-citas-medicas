<?php

namespace Database\Seeders;

use App\Models\Aseguradora;
use App\Models\Cita;
use App\Models\Clinica;
use App\Models\Especialidad;
use App\Models\Factura;
use App\Models\ListaEspera;
use App\Models\Medico;
use App\Models\Mensaje;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Seeder de datos DEMO para poblar el Dashboard.
 *
 * - Agrega 10 registros a cada módulo del sistema.
 * - Las CITAS se distribuyen en los últimos 7 días (para el gráfico
 *   "Actividad de Citas") y a lo largo de los últimos 6 meses
 *   (para el gráfico "Tendencia Mensual"), con estados y especialidades
 *   variados para que la dona y las barras muestren buena información.
 *
 * Ejecutar:  php artisan db:seed --class=DemoDashboardSeeder
 */
class DemoDashboardSeeder extends Seeder
{
    public function run(): void
    {
        // Usa la clínica del administrador (así los datos se ven en SU dashboard,
        // no en otra clínica del entorno multi-empresa).
        $clinicaId = User::whereNotNull('clinica_id')
            ->whereIn('rol', ['admin', 'recepcion', 'medico'])
            ->orderBy('id')
            ->value('clinica_id')
            ?? Clinica::first()?->id;

        if (!$clinicaId) {
            $this->command->error('No hay clínica disponible. Corre el SaaSSeeder primero.');
            return;
        }

        $this->command->info('Sembrando datos demo en la clínica #'.$clinicaId.' ...');

        $nombresEsp = [
            'Cardiología', 'Dermatología', 'Pediatría', 'Ginecología', 'Traumatología',
            'Oftalmología', 'Neurología', 'Odontología', 'Otorrinolaringología', 'Endocrinología',
        ];

        // 1. Especialidades (10)
        $espIds = [];
        foreach ($nombresEsp as $nombre) {
            $espIds[] = Especialidad::create([
                'clinica_id'  => $clinicaId,
                'nombre'      => $nombre . ' Demo',
                'color'       => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                'descripcion' => 'Especialidad de demostración para el dashboard',
                'activo'      => true,
            ])->id;
        }

        // 2. Aseguradoras (10)
        $aseIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $aseIds[] = Aseguradora::create([
                'clinica_id' => $clinicaId,
                'nombre'     => 'Aseguradora Demo ' . $i,
                'ruc'        => (string) random_int(1000000000, 9999999999),
                'activo'     => true,
            ])->id;
        }

        // 3. Médicos (10)
        $apellidos = ['Rojas', 'Mendoza', 'Salazar', 'Gutiérrez', 'Vargas', 'Flores', 'Castro', 'Ramírez', 'Torres', 'Fernández'];
        $medIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $medIds[] = Medico::create([
                'clinica_id'         => $clinicaId,
                'nombres'            => 'Dr. Demo ' . $i,
                'apellidos'          => $apellidos[$i - 1],
                'documento'          => (string) random_int(1000000, 9999999),
                'email'              => 'demo.medico' . $i . '_' . Str::random(4) . '@demo.test',
                'telefono'           => '7' . random_int(1000000, 9999999),
                'especialidad_id'    => $espIds[($i - 1) % count($espIds)],
                'numero_colegiatura' => 'CM-' . random_int(10000, 99999),
                'activo'             => true,
            ])->id;
        }

        // 4. Pacientes (10)
        $sexos  = ['M', 'F'];
        $pacIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $pacIds[] = Paciente::create([
                'clinica_id'      => $clinicaId,
                'nombres'         => 'Paciente Demo ' . $i,
                'apellidos'       => $apellidos[($i - 1) % count($apellidos)],
                'documento'       => (string) random_int(1000000, 9999999),
                'fecha_nacimiento'=> Carbon::now()->subYears(random_int(10, 70))->toDateString(),
                'sexo'            => $sexos[array_rand($sexos)],
                'email'           => 'demo.paciente' . $i . '_' . Str::random(4) . '@demo.test',
                'telefono'        => '6' . random_int(1000000, 9999999),
                'direccion'       => 'Av. Demo #' . random_int(100, 999),
                'aseguradora_id'  => $aseIds[array_rand($aseIds)],
                'activo'          => true,
            ])->id;
        }

        // 5. Usuarios (10)
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'clinica_id' => $clinicaId,
                'name'       => 'Usuario Demo ' . $i,
                'email'      => 'demo.user' . $i . '_' . Str::random(4) . '@demo.test',
                'password'   => Hash::make('password'),
                'rol'        => ['admin', 'medico', 'recepcion'][array_rand(['admin', 'medico', 'recepcion'])],
                'telefono'   => '7' . random_int(1000000, 9999999),
                'activo'     => true,
            ]);
        }

        // 6. CITAS distribuidas en el tiempo -------------------------------
        $estados   = ['pendiente', 'confirmada', 'atendida', 'cancelada'];
        $horas     = ['08:00', '09:30', '10:00', '11:15', '14:00', '15:30', '16:45'];
        $citasHoy7 = [];   // guardamos algunas para generar recetas/facturas

        // 6a. Últimos 7 días (incluyendo hoy): 2-4 citas por día -> alimenta "Actividad de Citas"
        for ($d = 6; $d >= 0; $d--) {
            $fecha  = Carbon::today()->subDays($d);
            $cuantas = random_int(2, 4);
            for ($c = 0; $c < $cuantas; $c++) {
                $citasHoy7[] = Cita::create([
                    'clinica_id'  => $clinicaId,
                    'paciente_id' => $pacIds[array_rand($pacIds)],
                    'medico_id'   => $medIds[array_rand($medIds)],
                    'fecha'       => $fecha->toDateString(),
                    'hora'        => $horas[array_rand($horas)],
                    'estado'      => $estados[array_rand($estados)],
                    'motivo'      => 'Consulta demo ' . $fecha->format('d/m'),
                ]);
            }
        }

        // 6b. Últimos 6 meses: 4-6 citas por mes -> alimenta "Tendencia Mensual"
        for ($m = 5; $m >= 1; $m--) {
            $base    = Carbon::today()->subMonths($m)->startOfMonth();
            $cuantas = random_int(4, 6);
            for ($c = 0; $c < $cuantas; $c++) {
                $dia = (clone $base)->addDays(random_int(0, 26));
                Cita::create([
                    'clinica_id'  => $clinicaId,
                    'paciente_id' => $pacIds[array_rand($pacIds)],
                    'medico_id'   => $medIds[array_rand($medIds)],
                    'fecha'       => $dia->toDateString(),
                    'hora'        => $horas[array_rand($horas)],
                    'estado'      => $estados[array_rand($estados)],
                    'motivo'      => 'Consulta demo ' . $dia->format('d/m'),
                ]);
            }
        }

        // 6c. Algunas citas próximas (agenda) -> alimenta "Agenda Próxima"
        for ($d = 1; $d <= 5; $d++) {
            Cita::create([
                'clinica_id'  => $clinicaId,
                'paciente_id' => $pacIds[array_rand($pacIds)],
                'medico_id'   => $medIds[array_rand($medIds)],
                'fecha'       => Carbon::today()->addDays($d)->toDateString(),
                'hora'        => $horas[array_rand($horas)],
                'estado'      => ['pendiente', 'confirmada'][array_rand(['pendiente', 'confirmada'])],
                'motivo'      => 'Cita agendada demo',
            ]);
        }

        // 7. Recetas (10) — usando las citas recientes
        $muestraCitas = array_slice($citasHoy7, 0, 10);
        foreach ($muestraCitas as $i => $cita) {
            Receta::create([
                'clinica_id'  => $clinicaId,
                'numero'      => 'REC-' . strtoupper(Str::random(8)),
                'paciente_id' => $cita->paciente_id,
                'medico_id'   => $cita->medico_id,
                'cita_id'     => $cita->id,
                'fecha'       => $cita->fecha,
                'diagnostico' => 'Diagnóstico demo ' . ($i + 1),
                'indicaciones'=> 'Reposo e hidratación. Control en 7 días.',
            ]);
        }

        // 8. Facturas (10)
        $metodos = ['efectivo', 'tarjeta', 'transferencia'];
        $estFac  = ['pendiente', 'pagada', 'anulada'];
        foreach ($muestraCitas as $i => $cita) {
            $subtotal = random_int(80, 400);
            $impuesto = round($subtotal * 0.18, 2);
            Factura::create([
                'clinica_id'  => $clinicaId,
                'numero'      => 'FAC-' . strtoupper(Str::random(8)),
                'paciente_id' => $cita->paciente_id,
                'cita_id'     => $cita->id,
                'fecha'       => $cita->fecha,
                'subtotal'    => $subtotal,
                'descuento'   => 0,
                'impuesto'    => $impuesto,
                'total'       => $subtotal + $impuesto,
                'estado'      => $estFac[array_rand($estFac)],
                'metodo_pago' => $metodos[array_rand($metodos)],
            ]);
        }

        // 9. Lista de espera (10)
        $prioridades = ['baja', 'media', 'alta', 'urgente'];
        $estLista    = ['esperando', 'llamado', 'atendido', 'cancelado'];
        for ($i = 1; $i <= 10; $i++) {
            ListaEspera::create([
                'clinica_id'      => $clinicaId,
                'paciente_id'     => $pacIds[array_rand($pacIds)],
                'especialidad_id' => $espIds[array_rand($espIds)],
                'medico_id'       => $medIds[array_rand($medIds)],
                'prioridad'       => $prioridades[array_rand($prioridades)],
                'estado'          => $estLista[array_rand($estLista)],
                'motivo'          => 'En espera de atención demo ' . $i,
            ]);
        }

        // 10. Mensajes (10) — entre usuarios existentes
        $userIds = User::where('clinica_id', $clinicaId)->pluck('id')->all();
        if (count($userIds) >= 2) {
            for ($i = 1; $i <= 10; $i++) {
                $rem = $userIds[array_rand($userIds)];
                do {
                    $des = $userIds[array_rand($userIds)];
                } while ($des === $rem);

                Mensaje::create([
                    'clinica_id'     => $clinicaId,
                    'remitente_id'   => $rem,
                    'destinatario_id'=> $des,
                    'asunto'         => 'Mensaje demo ' . $i,
                    'cuerpo'         => 'Este es un mensaje de demostración número ' . $i . ' para el dashboard.',
                    'leido'          => (bool) random_int(0, 1),
                ]);
            }
        }

        $this->command->info('DemoDashboardSeeder: datos de demostración creados correctamente.');
    }
}
