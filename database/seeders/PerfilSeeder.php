<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    public function run(): void
    {
        $perfiles = [
            [
                'nombre' => 'Administrador',
                'descripcion' => 'Acceso total al sistema',
                'modulos' => ['dashboard', 'remitos', 'reclamos', 'observaciones', 'proveedores', 'productos', 'informes', 'usuarios', 'perfiles'],
            ],
            [
                'nombre' => 'Operador',
                'descripcion' => 'Acceso a operaciones diarias',
                'modulos' => ['dashboard', 'remitos', 'reclamos', 'observaciones'],
            ],
            [
                'nombre' => 'Consulta',
                'descripcion' => 'Acceso de solo lectura a informes',
                'modulos' => ['dashboard', 'informes'],
            ],
        ];

        foreach ($perfiles as $perfilData) {
            $perfilId = DB::table('perfiles')->insertGetId([
                'nombre' => $perfilData['nombre'],
                'descripcion' => $perfilData['descripcion'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($perfilData['modulos'] as $modulo) {
                DB::table('perfil_modulo')->insert([
                    'perfil_id' => $perfilId,
                    'modulo' => $modulo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
