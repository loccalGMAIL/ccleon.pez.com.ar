<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logistica', function (Blueprint $table) {
            $table->string('nro_factura', 5)->nullable()->after('rto');
        });
    }

    public function down(): void
    {
        Schema::table('logistica', function (Blueprint $table) {
            $table->dropColumn('nro_factura');
        });
    }
};
