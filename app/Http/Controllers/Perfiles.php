<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\PerfilModulo;
use App\Models\PerfilProveedor;
use App\Models\PerfilRestriccionModulo;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class Perfiles extends Controller
{
    public function index()
    {
        $titulo = 'Perfiles';
        $items = Perfil::withCount(['modulos', 'usuarios'])->get();
        return view('modules.perfiles.index', compact('titulo', 'items'));
    }

    public function create()
    {
        $titulo = 'Nuevo Perfil';
        $modulos = config('modulos');
        return view('modules.perfiles.create', compact('titulo', 'modulos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:perfiles,nombre',
            'descripcion' => 'nullable|string|max:255',
            'modulos' => 'required|array|min:1',
        ]);

        $perfil = Perfil::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        foreach ($request->modulos as $modulo) {
            PerfilModulo::create([
                'perfil_id' => $perfil->id,
                'modulo' => $modulo,
            ]);
        }

        AuditLog::registrar('perfiles', 'crear', "Creo perfil {$perfil->nombre}", 'Perfil', $perfil->id, null, array_merge($perfil->toArray(), ['modulos' => $request->modulos]));

        return to_route('perfiles');
    }

    public function edit(string $id)
    {
        $titulo = 'Editar Perfil';
        $item = Perfil::with('modulos')->findOrFail($id);
        $modulos = config('modulos');
        $modulosSeleccionados = $item->modulos->pluck('modulo')->toArray();
        return view('modules.perfiles.create', compact('titulo', 'item', 'modulos', 'modulosSeleccionados'));
    }

    public function update(Request $request, string $id)
    {
        $perfil = Perfil::findOrFail($id);
        $datosAnteriores = array_merge($perfil->toArray(), ['modulos' => $perfil->modulos->pluck('modulo')->toArray()]);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:perfiles,nombre,' . $perfil->id,
            'descripcion' => 'nullable|string|max:255',
            'modulos' => 'required|array|min:1',
        ]);

        // Proteccion: impedir que el usuario remueva el modulo 'perfiles' de su propio perfil
        if (Auth::user()->perfil_id == $perfil->id && !in_array('perfiles', $request->modulos)) {
            return back()->withInput()->withErrors(['modulos' => 'No puedes remover el acceso a Perfiles de tu propio perfil.']);
        }

        $perfil->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        // Sincronizar modulos: eliminar existentes e insertar nuevos
        PerfilModulo::where('perfil_id', $perfil->id)->delete();
        foreach ($request->modulos as $modulo) {
            PerfilModulo::create([
                'perfil_id' => $perfil->id,
                'modulo' => $modulo,
            ]);
        }

        AuditLog::registrar('perfiles', 'editar', "Edito perfil {$perfil->nombre}", 'Perfil', $perfil->id, $datosAnteriores, array_merge($perfil->fresh()->toArray(), ['modulos' => $request->modulos]));

        return to_route('perfiles');
    }

    public function destroy(string $id)
    {
        try {
            $perfil = Perfil::withCount('usuarios')->findOrFail($id);

            if ($perfil->usuarios_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el perfil porque tiene ' . $perfil->usuarios_count . ' usuario(s) asignado(s).'
                ]);
            }

            $datosAnteriores = array_merge($perfil->toArray(), ['modulos' => $perfil->modulos->pluck('modulo')->toArray()]);
            $nombre = $perfil->nombre;
            $perfil->delete();

            AuditLog::registrar('perfiles', 'eliminar', "Elimino perfil {$nombre}", 'Perfil', (int) $id, $datosAnteriores);

            return response()->json(['success' => true, 'message' => 'Perfil eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el perfil']);
        }
    }

    public function restricciones()
    {
        $titulo = 'Restricciones de Proveedores';
        $perfiles = Perfil::orderBy('nombre')->get();
        $proveedores = Proveedor::where('estadoProveedor', 1)->orderBy('razonSocialProveedor')->get();
        $modulosDisponibles = [
            'logistica' => 'Logistica',
            'remitos' => 'Remitos',
            'productos' => 'Productos',
            'dashboard' => 'Dashboard',
        ];

        return view('modules.perfiles.restricciones', compact('titulo', 'perfiles', 'proveedores', 'modulosDisponibles'));
    }

    public function getRestriccion($perfilId)
    {
        $perfil = Perfil::with(['proveedoresPermitidos', 'modulosRestringidos'])->findOrFail($perfilId);

        return response()->json([
            'success' => true,
            'proveedores' => $perfil->proveedoresPermitidos->pluck('proveedores_id')->toArray(),
            'modulos' => $perfil->modulosRestringidos->pluck('modulo')->toArray(),
        ]);
    }

    public function guardarRestriccion(Request $request)
    {
        $request->validate([
            'perfil_id' => 'required|exists:perfiles,id',
            'proveedores' => 'nullable|array',
            'proveedores.*' => 'exists:proveedores,id',
            'modulos' => 'nullable|array',
        ]);

        $perfil = Perfil::findOrFail($request->perfil_id);
        $proveedores = $request->input('proveedores', []);
        $modulos = $request->input('modulos', []);

        $antes = [
            'proveedores' => $perfil->proveedoresPermitidos()->pluck('proveedores_id')->toArray(),
            'modulos' => $perfil->modulosRestringidos()->pluck('modulo')->toArray(),
        ];

        // Sincronizar proveedores permitidos
        PerfilProveedor::where('perfil_id', $perfil->id)->delete();
        foreach ($proveedores as $provId) {
            PerfilProveedor::create([
                'perfil_id' => $perfil->id,
                'proveedores_id' => $provId,
            ]);
        }

        // Sincronizar modulos restringidos
        PerfilRestriccionModulo::where('perfil_id', $perfil->id)->delete();
        foreach ($modulos as $modulo) {
            PerfilRestriccionModulo::create([
                'perfil_id' => $perfil->id,
                'modulo' => $modulo,
            ]);
        }

        $despues = [
            'proveedores' => $proveedores,
            'modulos' => $modulos,
        ];

        AuditLog::registrar('perfiles', 'restriccion_proveedores', "Actualizo restricciones de proveedores del perfil {$perfil->nombre}", 'Perfil', $perfil->id, $antes, $despues);

        return response()->json(['success' => true, 'message' => 'Restricciones guardadas correctamente']);
    }
}
