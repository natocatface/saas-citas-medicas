<?php

namespace Database\Seeders;

use App\Models\Aseguradora;
use App\Models\Cita;
use App\Models\Clinica;
use App\Models\Especialidad;
use App\Models\Factura;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Receta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $clinica = Clinica::first();
        $clinicaId = $clinica ? $clinica->id : null;

        if (!$clinicaId) {
            $this->command->error('No hay clínica disponible. Corre el SaaSSeeder primero.');
            return;
        }

        // 1. Especialidades (10)
        $espIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $espIds[] = Especialidad::create([
                'clinica_id' => $clinicaId,
                'nombre' => 'Esp. Dashboard ' . Str::random(5),
                'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT),
                'descripcion' => 'Descripción demo para dashboard',
                'activo' => true
            ])->id;
        }

        // 2. Aseguradoras (10)
        $aseIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $aseIds[] = Aseguradora::create([
                'clinica_id' => $clinicaId,
                'nombre' => 'Aseg. Dashboard ' . Str::random(5),
                'ruc' => (string) random_int(1000000000, 9999999999),
                'activo' => true
            ])->id;
        }

        // 3. Medicos (10)
        $medIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $medIds[] = Medico::create([
                'clinica_id' => $clinicaId,
                'nombres' => 'Médico DB ' . $i,
                'apellidos' => Str::random(5),
                'documento' => (string) random_int(1000000, 9999999),
                'email' => 'db.medico'.$i.'_'.Str::random(4).'@test.com',
                'telefono' => '7' . random_int(1000000, 9999999),
                'especialidad_id' => $espIds[array_rand($espIds)],
                'numero_colegiatura' => 'CM-' . random_int(10000, 99999),
                'activo' => true,
            ])->id;
        }

        // 4. Pacientes (10)
        $pacIds = [];
        $sexos = ['M', 'F'];
        for ($i = 1; $i <= 10; $i++) {
            $pacIds[] = Paciente::create([
                'clinica_id' => $clinicaId,
                'nombres' => 'Paciente DB ' . $i,
                'apellidos' => Str::random(5),
                'documento' => (string) random_int(1000000, 9999999),
                'fecha_nacimiento' => Carbon::now()->subYears(random_int(10, 60))->toDateString(),
                'sexo' => $sexos[array_rand($sexos)],
                'email' => 'db.paciente'.$i.'_'.Str::random(4).'@test.com',
                'telefono' => '6' . random_int(1000000, 9999999),
                'aseguradora_id' => $aseIds[array_rand($aseIds)],
                'activo' => true,
            ])->id;
        }

        // 5. Citas (10 distribuidas en los ultimos 7 dias, hoy y proximos dias)
        $estados = ['pendiente', 'confirmada', 'atendida', 'cancelada'];
        for ($i = 0; $i < 10; $i++) {
            // Distribuir entre hoy - 6 dias, hoy, y hoy + 3 dias
            $offset = random_int(-6, 3);
            $fecha = Carbon::today()->addDays($offset);
            
            // Si la cita es en el futuro, no debería estar 'atendida'
            $estado = ($offset > 0) ? 'pendiente' : $estados[array_rand($estados)];

            $cita = Cita::create([
                'clinica_id' => $clinicaId,
                'paciente_id' => $pacIds[array_rand($pacIds)],
                'medico_id' => $medIds[array_rand($medIds)],
                'fecha' => $fecha->toDateString(),
                'hora' => '10:00',
                'estado' => $estado,
                'motivo' => 'Motivo dashboard ' . $i,
            ]);

            // 6. Recetas (1 para cada cita)
            Receta::create([
                'clinica_id' => $clinicaId,
                'numero' => 'REC-' . strtoupper(Str::random(6)),
                'cita_id' => $cita->id,
                'paciente_id' => $cita->paciente_id,
                'medico_id' => $cita->medico_id,
                'fecha' => $fecha->toDateString(),
                'diagnostico' => 'Diagnóstico ' . $i,
                'indicaciones' => 'Tomar mucha agua ' . $i,
            ]);

            // 7. Facturas (1 para cada cita)
            Factura::create([
                'clinica_id' => $clinicaId,
                'cita_id' => $cita->id,
                'paciente_id' => $cita->paciente_id,
                'numero' => 'FAC-' . random_int(1000, 9999),
                'fecha' => $fecha->toDateString(),
                'subtotal' => 100.00,
                'impuesto' => 18.00,
                'total' => 118.00,
                'estado' => 'pagada',
            ]);
        }
    }
}
