<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Usuarios extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titulo = 'Usuarios';
        $items = User::with('perfil')->get();
        return view('modules.usuarios.index', compact('titulo', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titulo = 'Crear Usuario';
        $perfiles = Perfil::all();
        return view('modules.usuarios.create', compact('titulo', 'perfiles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        User::create(
            [
                'name' => request('name'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
                'activo' => true,
                'perfil_id' => request('perfil_id')
            ]
        );
        return to_route('usuarios');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $titulo = 'Editar Usuario';
        $item = User::find($id);
        $perfiles = Perfil::all();
        return view('modules.usuarios.edit', compact('titulo', 'item', 'perfiles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = User::find($id);
        $item->name = request('name');
        $item->email = request('email');
        $item->password = Hash::make(request('password'));
        $item->perfil_id = request('perfil_id');
        $item->save();
        return to_route('usuarios');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = User::findOrFail($id);
            $item->delete();

            return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el usuario']);
        }
    }

    public function estado(Request $request, $id)
    {
        try {
            $item = User::findOrFail($id);
            $item->activo = $request->input('activo'); // Actualizar el estado con el valor recibido
            $item->save();

            return response()->json(['success' => true, 'message' => 'Estado de usuario actualizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }
}
