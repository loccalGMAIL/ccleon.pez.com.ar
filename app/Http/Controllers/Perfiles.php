<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\PerfilModulo;
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
}
