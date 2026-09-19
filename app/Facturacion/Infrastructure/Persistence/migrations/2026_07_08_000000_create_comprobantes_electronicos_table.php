<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes_electronicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinica_id')->nullable()->index();
            $table->foreignId('factura_id')->nullable()->index(); // vínculo con la Factura del ERP

            $table->string('pais', 2)->index();          // PE, CO, CL, AR, MX
            $table->string('tipo', 20);                   // factura, boleta, nota_credito...
            $table->string('serie', 8);
            $table->string('correlativo', 12);
            $table->string('numero_completo', 24)->index();
            $table->string('moneda', 3)->default('PEN');

            $table->string('emisor_ruc', 15);
            $table->string('receptor_doc', 10)->nullable();
            $table->string('receptor_numero', 20)->nullable();
            $table->string('receptor_nombre')->nullable();

            $table->date('fecha_emision');
            $table->decimal('gravado', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Estado tributario y respuesta del organismo
            $table->string('estado', 20)->default('borrador')->index();
            $table->string('codigo_respuesta', 10)->nullable();
            $table->text('mensaje')->nullable();
            $table->string('hash_cpe', 100)->nullable();

            // Artefactos almacenados
            $table->string('ruta_xml')->nullable();
            $table->string('ruta_cdr')->nullable();
            $table->string('ruta_pdf')->nullable();
            $table->json('observaciones')->nullable();
            $table->unsignedSmallInteger('intentos')->default(0);

            $table->timestamps();

            // Un correlativo no puede repetirse dentro de una serie por emisor.
            $table->unique(['emisor_ruc', 'serie', 'correlativo'], 'uq_comprobante_serie_corr');
        });

        Schema::create('comprobante_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_id')->nullable()->index();
            $table->string('numero_completo', 24)->index();
            $table->string('evento', 50);   // emision.iniciada, emision.resultado, anulacion...
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobante_eventos');
        Schema::dropIfExists('comprobantes_electronicos');
    }
};
