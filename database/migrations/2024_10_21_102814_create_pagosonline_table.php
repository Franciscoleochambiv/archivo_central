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
        Schema::create('pagosonline', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id');
            $table->decimal('monto', 10, 2);
            $table->string('token');
            $table->timestamp('fecha_pago')->useCurrent();
            $table->timestamp('fecha_vigencia')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagosonline');
    }
};
