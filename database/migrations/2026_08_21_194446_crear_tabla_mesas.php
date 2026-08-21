<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->enum('ubicacion', ['A', 'B', 'C', 'D']);
            $table->unsignedInteger('numero');
            $table->unsignedInteger('capacidad');
            $table->timestamps();

            $table->unique(['ubicacion', 'numero']);
            $table->index(['ubicacion', 'capacidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
