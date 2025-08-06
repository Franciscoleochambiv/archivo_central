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
        Schema::create('oficinas', function (Blueprint $table) {
             $table->id();
             $table->foreignId('entidad_id')->constrained('entidades')->onDelete('cascade');
             $table->string('nombre'); // Ej. Tesorería, Informática
             $table->string('seccion')->nullable(); // Ej. Archivo A
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oficinas');
    }
};
