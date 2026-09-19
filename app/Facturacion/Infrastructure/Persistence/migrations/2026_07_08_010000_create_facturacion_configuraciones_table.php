<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_configuraciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinica_id')->nullable()->unique();

            $table->boolean('habilitado')->default(false);        // false = modo simulado
            $table->string('modo', 12)->default('beta');          // beta (homologación) | produccion

            // Datos del emisor
            $table->string('ruc', 11)->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ubigeo', 6)->nullable();

            // Tributos y numeración
            $table->string('moneda', 3)->default('PEN');
            $table->decimal('igv_tasa', 5, 2)->default(18.00);
            $table->string('serie_factura', 4)->default('F001');
            $table->string('serie_boleta', 4)->default('B001');
            $table->string('serie_nota_credito', 4)->default('FC01');

            // Credenciales SUNAT (se guardan cifradas vía cast del modelo)
            $table->string('sol_usuario')->nullable();
            $table->text('sol_clave')->nullable();
            $table->string('certificado_path')->nullable();       // ruta del .pfx/.pem almacenado
            $table->text('certificado_clave')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_configuraciones');
    }
};
