<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedores_id')->constrained('proveedores');
            $table->date('fecha_pedido');
            $table->date('etd')->nullable();
            $table->date('eta')->nullable();
            $table->string('destino', 200)->nullable();
            $table->string('transporte', 200)->nullable();
            $table->date('arribo_confirmado')->nullable();
            $table->enum('estado', ['Pendiente', 'En transito', 'Arribado', 'Demorado', 'Cerrado'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistica');
    }
};
