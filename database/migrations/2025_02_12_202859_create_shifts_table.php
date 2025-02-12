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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['muestras', 'resultados']);
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->integer('number');
            $table->enum('status', ['espera', 'atendido', 'en proceso', 'cancelado'])->default('espera');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
