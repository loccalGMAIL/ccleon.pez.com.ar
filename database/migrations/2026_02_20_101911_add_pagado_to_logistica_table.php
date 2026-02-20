<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistica', function (Blueprint $table) {
            $table->enum('pago', ['Deuda', 'Pagado'])->default('Deuda')->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('logistica', function (Blueprint $table) {
            $table->dropColumn('pago');
        });
    }
};
