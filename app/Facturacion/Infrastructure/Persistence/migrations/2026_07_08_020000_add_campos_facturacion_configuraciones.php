<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturacion_configuraciones', function (Blueprint $table) {
            $table->string('departamento')->nullable()->after('ubigeo');
            $table->string('provincia')->nullable()->after('departamento');
            $table->string('distrito')->nullable()->after('provincia');
            $table->boolean('emitir_automatico')->default(false)->after('habilitado');
            $table->string('driver', 20)->default('ninguno')->after('modo'); // ninguno | greenter
            $table->string('certificado_ruta')->nullable()->after('certificado_path'); // ruta absoluta a un .pem
        });
    }

    public function down(): void
    {
        Schema::table('facturacion_configuraciones', function (Blueprint $table) {
            $table->dropColumn(['departamento', 'provincia', 'distrito', 'emitir_automatico', 'driver', 'certificado_ruta']);
        });
    }
};
