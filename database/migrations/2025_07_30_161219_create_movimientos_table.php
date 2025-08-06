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
        Schema::create('movimientos', function (Blueprint $table) {
             $table->id();
             $table->foreignId('registro_documental_id')->constrained('registros_documentales')->onDelete('cascade');
             $table->enum('tipo', ['PRESTAMO', 'DEVOLUCION']);
             $table->string('usuario'); // Persona que lo solicitó
             $table->text('comentario')->nullable();
             $table->timestamp('fecha');
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
