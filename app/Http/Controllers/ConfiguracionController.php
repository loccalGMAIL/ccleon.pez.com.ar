<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Camion;
use App\Models\User;
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

    public function auditLog(Request $request)
    {
        $titulo = 'Registro de Actividad';

        $query = AuditLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $logs = $query->paginate(50);

        $usuarios = User::select('id', 'name')->orderBy('name')->get();
        $modulos = array_keys(config('modulos'));
        $acciones = ['crear', 'editar', 'eliminar', 'cambiar_estado', 'login', 'logout', 'reiniciar_numeracion', 'editar_perfil', 'actualizar_cotizacion'];

        return view('modules.configuracion.audit-log', compact('titulo', 'logs', 'usuarios', 'modulos', 'acciones'));
    }

    public function auditLogDetalle($id)
    {
        $log = AuditLog::findOrFail($id);
        return response()->json($log);
    }

    public function reiniciarNumeracion()
    {
        try {
            DB::transaction(function () {
                Camion::query()->update(['contador' => 1]);
            });

            Log::info('Numeracion de remitos reiniciada por usuario: ' . auth()->user()->name);

            AuditLog::registrar('configuracion', 'reiniciar_numeracion', 'Reinicio la numeracion de remitos');

            return redirect()->route('configuracion.numeracion')->with('success', 'Numeracion reiniciada correctamente. Todos los contadores fueron restablecidos a 1.');
        } catch (\Exception $e) {
            Log::error('Error al reiniciar numeracion: ' . $e->getMessage());

            return redirect()->route('configuracion.numeracion')->with('error', 'Error al reiniciar la numeracion: ' . $e->getMessage());
        }
    }
}
