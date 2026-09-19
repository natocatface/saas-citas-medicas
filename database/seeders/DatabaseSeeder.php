<?php

namespace Database\Seeders;

use App\Models\Aseguradora;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----- Usuarios -----
        User::updateOrCreate(
            ['email' => 'admin@citasmedicas.test'],
            [
                'name' => 'Admin Vito',
                'password' => Hash::make('password'),
                'rol' => 'admin',
                'telefono' => '70000000',
                'activo' => true,
            ]
        );
        User::updateOrCreate(
            ['email' => 'recepcion@citasmedicas.test'],
            [
                'name' => 'Recepción Clínica',
                'password' => Hash::make('password'),
                'rol' => 'recepcion',
                'activo' => true,
            ]
        );

        // ----- Especialidades -----
        $especialidades = [
            ['Cardiología', '#ef4444'],
            ['Pediatría', '#f59e0b'],
            ['Dermatología', '#ec4899'],
            ['Ginecología', '#8b5cf6'],
            ['Traumatología', '#3b82f6'],
            ['Oftalmología', '#06b6d4'],
            ['Neurología', '#6366f1'],
            ['Odontología', '#10b981'],
            ['Medicina General', '#14b8a6'],
            ['Otorrinolaringología', '#f97316'],
        ];
        $espIds = [];
        foreach ($especialidades as [$nombre, $color]) {
            $espIds[] = Especialidad::updateOrCreate(
                ['nombre' => $nombre],
                ['color' => $color, 'descripcion' => "Atención en {$nombre}", 'activo' => true]
            )->id;
        }

        // ----- Aseguradoras -----
        $aseguradoras = ['Seguros Salud Total', 'MediPlan', 'VidaSegura', 'AsisMed', 'Particular'];
        $aseIds = [];
        foreach ($aseguradoras as $nombre) {
            $aseIds[] = Aseguradora::updateOrCreate(
                ['nombre' => $nombre],
                ['ruc' => (string) random_int(1000000000, 9999999999), 'activo' => true]
            )->id;
        }

        // ----- Medicos (122) -----
        if (Medico::count() === 0) {
            $nombresM = ['Carla', 'Jesús', 'Ángel', 'María', 'Luis', 'Ana', 'Carlos', 'Lucía', 'Pedro', 'Sofía', 'Diego', 'Valeria', 'Jorge', 'Camila', 'Andrés', 'Daniela', 'Fernando', 'Paola', 'Ricardo', 'Gabriela'];
            $apellidosM = ['Estrada', 'Correa Segundo', 'Pantoja', 'Vargas', 'Rojas', 'Mendoza', 'Flores', 'Gutiérrez', 'Salazar', 'Reyes', 'Cabrera', 'Núñez', 'Paredes', 'Ibáñez', 'Suárez'];
            for ($i = 0; $i < 122; $i++) {
                Medico::create([
                    'nombres' => $nombresM[array_rand($nombresM)],
                    'apellidos' => $apellidosM[array_rand($apellidosM)],
                    'documento' => (string) random_int(1000000, 9999999),
                    'email' => 'medico'.$i.'@citasmedicas.test',
                    'telefono' => '7'.random_int(1000000, 9999999),
                    'especialidad_id' => $espIds[array_rand($espIds)],
                    'numero_colegiatura' => 'CM-'.random_int(10000, 99999),
                    'activo' => true,
                ]);
            }
        }

        // ----- Pacientes (127) -----
        if (Paciente::count() === 0) {
            $nombresP = ['Manuel', 'Pablo', 'Rosa', 'Juan', 'Elena', 'Miguel', 'Patricia', 'Roberto', 'Carmen', 'Víctor', 'Laura', 'Sergio', 'Natalia', 'Hugo', 'Isabel', 'Raúl', 'Verónica', 'Óscar', 'Teresa', 'Marcelo'];
            $apellidosP = ['Varela', 'Guardado', 'Quispe', 'Mamani', 'Torrez', 'Calderón', 'Aguilar', 'Peña', 'Romero', 'Delgado', 'Cruz', 'Vega', 'Soto', 'Campos', 'Fuentes'];
            $sexos = ['M', 'F'];
            for ($i = 0; $i < 127; $i++) {
                Paciente::create([
                    'nombres' => $nombresP[array_rand($nombresP)],
                    'apellidos' => $apellidosP[array_rand($apellidosP)],
                    'documento' => (string) random_int(1000000, 9999999),
                    'fecha_nacimiento' => Carbon::now()->subYears(random_int(1, 85))->subDays(random_int(0, 364))->toDateString(),
                    'sexo' => $sexos[array_rand($sexos)],
                    'email' => 'paciente'.$i.'@correo.test',
                    'telefono' => '6'.random_int(1000000, 9999999),
                    'aseguradora_id' => $aseIds[array_rand($aseIds)],
                    'activo' => true,
                ]);
            }
        }

        // ----- Citas (~391) -----
        if (Cita::count() === 0) {
            $medicoIds = Medico::pluck('id')->all();
            $pacienteIds = Paciente::pluck('id')->all();
            $motivos = ['Consulta general', 'Control', 'Dolor abdominal', 'Chequeo anual', 'Seguimiento', 'Primera consulta', 'Resultados de laboratorio', 'Vacunación'];
            $horas = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'];

            for ($i = 0; $i < 391; $i++) {
                // Distribuir: -30 dias a +14 dias, con mas densidad reciente
                $offset = random_int(-30, 14);
                $fecha = Carbon::today()->addDays($offset);

                // Estado segun fecha
                if ($offset < 0) {
                    $estado = [['atendida', 70], ['cancelada', 15], ['confirmada', 15]];
                } else {
                    $estado = [['pendiente', 45], ['confirmada', 45], ['cancelada', 10]];
                }
                $estadoFinal = $this->pesado($estado);

                Cita::create([
                    'paciente_id' => $pacienteIds[array_rand($pacienteIds)],
                    'medico_id' => $medicoIds[array_rand($medicoIds)],
                    'fecha' => $fecha->toDateString(),
                    'hora' => $horas[array_rand($horas)],
                    'estado' => $estadoFinal,
                    'motivo' => $motivos[array_rand($motivos)],
                ]);
            }
        }

        // ----- Plataforma SaaS al final: clínicas, planes, superadmin y backfill de clinica_id -----
        $this->call(SaaSSeeder::class);
    }

    private function pesado(array $opciones): string
    {
        $total = array_sum(array_column($opciones, 1));
        $r = random_int(1, $total);
        $acc = 0;
        foreach ($opciones as [$valor, $peso]) {
            $acc += $peso;
            if ($r <= $acc) {
                return $valor;
            }
        }
        return $opciones[0][0];
    }
}
