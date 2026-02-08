<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar columna perfil_id nullable inicialmente
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('perfil_id')->nullable()->after('rol')->constrained('perfiles');
        });

        // Crear perfiles por defecto si no existen
        $adminId = DB::table('perfiles')->where('nombre', 'Administrador')->value('id');
        if (!$adminId) {
            $adminId = DB::table('perfiles')->insertGetId([
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Asignar todos los modulos al perfil Administrador
            $modulos = ['dashboard', 'remitos', 'reclamos', 'observaciones', 'proveedores', 'productos', 'informes', 'usuarios', 'perfiles'];
            foreach ($modulos as $modulo) {
                DB::table('perfil_modulo')->insert([
                    'perfil_id' => $adminId,
                    'modulo' => $modulo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $operadorId = DB::table('perfiles')->where('nombre', 'Operador')->value('id');
        if (!$operadorId) {
            $operadorId = DB::table('perfiles')->insertGetId([
                'nombre' => 'Operador',
                'descripcion' => 'Acceso a operaciones diarias',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $modulosOperador = ['dashboard', 'remitos', 'reclamos', 'observaciones'];
            foreach ($modulosOperador as $modulo) {
                DB::table('perfil_modulo')->insert([
                    'perfil_id' => $operadorId,
                    'modulo' => $modulo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Mapear usuarios existentes segun su rol
        DB::table('users')->where('rol', 'admin')->update(['perfil_id' => $adminId]);
        DB::table('users')->where('rol', 'usuario')->update(['perfil_id' => $operadorId]);
        // Usuarios sin rol asignado van a Operador
        DB::table('users')->whereNull('perfil_id')->update(['perfil_id' => $operadorId]);

        // Hacer perfil_id NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('perfil_id')->nullable(false)->change();
        });

        // Eliminar columna rol
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rol');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['admin', 'usuario'])->default('usuario')->after('email_verified_at');
        });

        // Restaurar roles basado en perfil
        $adminId = DB::table('perfiles')->where('nombre', 'Administrador')->value('id');
        if ($adminId) {
            DB::table('users')->where('perfil_id', $adminId)->update(['rol' => 'admin']);
        }
        DB::table('users')->where('rol', '')->orWhereNull('rol')->update(['rol' => 'usuario']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('perfil_id');
        });
    }
};
