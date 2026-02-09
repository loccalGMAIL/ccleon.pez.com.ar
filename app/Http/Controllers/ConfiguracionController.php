<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Camion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $titulo = 'Configuracion General';

        return view('modules.configuracion.index', compact('titulo'));
    }

    public function numeracion()
    {
        $titulo = 'Numeracion de Remitos';
        $camiones = Camion::with('proveedor')->get();

        return view('modules.configuracion.numeracion', compact('titulo', 'camiones'));
    }

    public function reiniciarNumeracion()
    {
        try {
            DB::transaction(function () {
                Camion::query()->update(['contador' => 1]);
            });

            Log::info('Numeracion de remitos reiniciada por usuario: ' . auth()->user()->name);

            return redirect()->route('configuracion.numeracion')->with('success', 'Numeracion reiniciada correctamente. Todos los contadores fueron restablecidos a 1.');
        } catch (\Exception $e) {
            Log::error('Error al reiniciar numeracion: ' . $e->getMessage());

            return redirect()->route('configuracion.numeracion')->with('error', 'Error al reiniciar la numeracion: ' . $e->getMessage());
        }
    }
}
