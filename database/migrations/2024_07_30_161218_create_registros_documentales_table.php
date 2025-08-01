<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registros_documentales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('oficina_id')->constrained('oficinas')->onDelete('cascade');
                $table->foreignId('entidad_id')->constrained('entidades')->onDelete('cascade');         
                $table->string('periodo');
                $table->year('anio_elaboracion');
                $table->string('seccion');
                $table->string('fechas_extremos');
                $table->string('item');
                $table->string('nro_archivo');
                $table->string('unidad_conservacion');
                $table->string('serie_documental');
                $table->string('nro_comprobantes');
                $table->string('ubicacion_estante');
                $table->enum('valor_serie_documental', ['T', 'P']);
                $table->string('folios');
                $table->string('soporte_papel');
                $table->boolean('es_copia_original');
                $table->string('anio_extremo_inicio');
                $table->string('anio_extremo_fin');
                $table->string('color')->nullable();
                $table->text('observaciones')->nullable();
                $table->enum('estado_archivador', ['B', 'M', 'R']);
                $table->enum('ubicacion_actual', ['A.C', 'PRESTAMO', 'DEVUELTO']);
                $table->timestamps();
        });
    }

    /**
     * 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_documentales');
    }
};
