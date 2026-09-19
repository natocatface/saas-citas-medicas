<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = [
        'users', 'pacientes', 'medicos', 'especialidades', 'aseguradoras',
        'citas', 'facturas', 'recetas', 'lista_espera',
    ];

    public function up(): void
    {
        // 1) Agregar columna clinica_id (nullable) a cada tabla
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla) && ! Schema::hasColumn($tabla, 'clinica_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->unsignedBigInteger('clinica_id')->nullable()->after('id')->index();
                });
            }
        }

        // 2) Sembrar planes estándar si no existen
        if (DB::table('planes')->count() === 0) {
            $now = now();
            DB::table('planes')->insert([
                ['nombre' => 'Básico', 'precio' => 199, 'periodo' => 'mensual', 'max_medicos' => 2, 'max_usuarios' => 3, 'caracteristicas' => "1 consultorio\nCitas y pacientes\nCalendario\nSoporte por correo", 'destacado' => false, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
                ['nombre' => 'Profesional', 'precio' => 449, 'periodo' => 'mensual', 'max_medicos' => null, 'max_usuarios' => 10, 'caracteristicas' => "Hasta 5 consultorios\nMédicos ilimitados\nRecetas y facturación\nReportes y export\nSoporte prioritario", 'destacado' => true, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
                ['nombre' => 'Clínica', 'precio' => 899, 'periodo' => 'mensual', 'max_medicos' => null, 'max_usuarios' => null, 'caracteristicas' => "Consultorios ilimitados\nUsuarios y roles\nAseguradoras y convenios\nReportes avanzados\nSoporte 24/7", 'destacado' => false, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        // 3) Crear clínica por defecto y asignar todos los datos existentes
        $clinicaId = DB::table('clinicas')->where('slug', 'clinica-central')->value('id');
        if (! $clinicaId) {
            $planProf = DB::table('planes')->where('nombre', 'Profesional')->value('id');
            $clinicaId = DB::table('clinicas')->insertGetId([
                'nombre' => 'Clínica Central',
                'slug' => 'clinica-central',
                'email' => 'contacto@clinicacentral.test',
                'telefono' => '70000000',
                'direccion' => 'Av. Principal #100',
                'plan_id' => $planProf,
                'estado_suscripcion' => 'activa',
                'suscripcion_vence' => now()->addYear()->toDateString(),
                'color' => '#17b8cf',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4) Backfill: asignar clinica_id por defecto a filas existentes
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla)) {
                DB::table($tabla)->whereNull('clinica_id')->update(['clinica_id' => $clinicaId]);
            }
        }

        // Los superadmin no pertenecen a una clínica: se dejan en null al crearse.
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'clinica_id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropColumn('clinica_id');
                });
            }
        }
    }
};
