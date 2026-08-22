<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva_mesa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mesa_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['reserva_id', 'mesa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_mesa');
    }
};
