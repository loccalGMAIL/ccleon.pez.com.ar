<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfil_modulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfiles')->cascadeOnDelete();
            $table->string('modulo');
            $table->timestamps();

            $table->unique(['perfil_id', 'modulo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfil_modulo');
    }
};
