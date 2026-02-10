<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfil_proveedor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfiles')->onDelete('cascade');
            $table->foreignId('proveedores_id')->constrained('proveedores')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['perfil_id', 'proveedores_id']);
        });

        Schema::create('perfil_restriccion_modulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfiles')->onDelete('cascade');
            $table->string('modulo');
            $table->timestamps();
            $table->unique(['perfil_id', 'modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfil_restriccion_modulo');
        Schema::dropIfExists('perfil_proveedor');
    }
};
