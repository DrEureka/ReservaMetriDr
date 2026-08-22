<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('ubicacion', ['A', 'B', 'C', 'D']);
            $table->unsignedInteger('cantidad_personas');
            $table->enum('estado', ['confirmada', 'cancelada'])->default('confirmada');
            $table->timestamp('cancelada_at')->nullable();
            $table->timestamps();

            $table->index(['fecha', 'ubicacion']);
            $table->index('estado');
            $table->index(['fecha', 'hora_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
