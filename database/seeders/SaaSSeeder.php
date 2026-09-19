<?php

namespace Database\Seeders;

use App\Models\Clinica;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SaaSSeeder extends Seeder
{
    public function run(): void
    {
        // Planes (por si la migración no los creó)
        if (Plan::count() === 0) {
            foreach ([
                ['Básico', 199, 2, 3, "1 consultorio\nCitas y pacientes\nCalendario\nSoporte por correo", false],
                ['Profesional', 449, null, 10, "Hasta 5 consultorios\nMédicos ilimitados\nRecetas y facturación\nReportes y export\nSoporte prioritario", true],
                ['Clínica', 899, null, null, "Consultorios ilimitados\nUsuarios y roles\nAseguradoras y convenios\nReportes avanzados\nSoporte 24/7", false],
            ] as [$nombre, $precio, $maxMed, $maxUsr, $car, $dest]) {
                Plan::create([
                    'nombre' => $nombre, 'precio' => $precio, 'periodo' => 'mensual',
                    'max_medicos' => $maxMed, 'max_usuarios' => $maxUsr,
                    'caracteristicas' => $car, 'destacado' => $dest, 'activo' => true,
                ]);
            }
        }

        $planProf = Plan::where('nombre', 'Profesional')->value('id');
        $planBasico = Plan::where('nombre', 'Básico')->value('id');

        // Clínica principal (la que tiene los datos existentes)
        $central = Clinica::firstOrCreate(
            ['slug' => 'clinica-central'],
            [
                'nombre' => 'Clínica Central', 'email' => 'contacto@clinicacentral.test',
                'telefono' => '70000000', 'direccion' => 'Av. Principal #100',
                'plan_id' => $planProf, 'estado_suscripcion' => 'activa',
                'suscripcion_vence' => Carbon::now()->addYear(), 'color' => '#17b8cf', 'activo' => true,
            ]
        );

        // Clínicas demo adicionales (para el panel SaaS)
        Clinica::firstOrCreate(['slug' => 'centro-medico-norte'], [
            'nombre' => 'Centro Médico Norte', 'email' => 'info@cmnorte.test', 'telefono' => '71111111',
            'direccion' => 'Calle Norte #45', 'plan_id' => $planBasico, 'estado_suscripcion' => 'prueba',
            'suscripcion_vence' => Carbon::now()->addDays(18), 'color' => '#6366f1', 'activo' => true,
        ]);
        Clinica::firstOrCreate(['slug' => 'clinica-sur-salud'], [
            'nombre' => 'Clínica Sur Salud', 'email' => 'hola@sursalud.test', 'telefono' => '72222222',
            'direccion' => 'Av. Sur #320', 'plan_id' => $planProf, 'estado_suscripcion' => 'suspendida',
            'suscripcion_vence' => Carbon::now()->subDays(5), 'color' => '#10b981', 'activo' => true,
        ]);

        // Super Admin (sin clínica)
        User::updateOrCreate(
            ['email' => 'superadmin@citasmedicas.test'],
            [
                'name' => 'Super Admin', 'password' => Hash::make('password'),
                'rol' => 'superadmin', 'clinica_id' => null, 'activo' => true,
            ]
        );

        // Asegurar que admin/recepción pertenezcan a la clínica central
        User::where('email', 'admin@citasmedicas.test')->update(['clinica_id' => $central->id]);
        User::where('email', 'recepcion@citasmedicas.test')->update(['clinica_id' => $central->id]);

        // Backfill: cualquier dato sin clínica se asigna a la central
        foreach (['pacientes', 'medicos', 'especialidades', 'aseguradoras', 'citas', 'facturas', 'recetas', 'lista_espera'] as $tabla) {
            DB::table($tabla)->whereNull('clinica_id')->update(['clinica_id' => $central->id]);
        }
    }
}
