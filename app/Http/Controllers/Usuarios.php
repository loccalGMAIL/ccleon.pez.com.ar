<?php

namespace App\Http\Controllers;

use App\Models\Perfil;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $data = [
            'name' => request('name'),
            'email' => request('email'),
            'password' => Hash::make(request('password')),
            'activo' => true,
            'perfil_id' => request('perfil_id')
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('fotos_perfil', 'public');
            $data['foto'] = basename($data['foto']);
        }

        User::create($data);
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

        if ($request->hasFile('foto')) {
            if ($item->foto) {
                Storage::disk('public')->delete('fotos_perfil/' . $item->foto);
            }
            $item->foto = basename($request->file('foto')->store('fotos_perfil', 'public'));
        }

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

    public function miPerfil()
    {
        $titulo = 'Mi Perfil';
        $user = Auth::user();
        return view('modules.usuarios.mi-perfil', compact('titulo', 'user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $rules = [
            'foto' => 'nullable|image|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
        }

        $request->validate($rules, [
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'foto.image' => 'El archivo debe ser una imagen válida.',
            'foto.max' => 'La imagen no debe superar los 2 MB.',
        ]);

        $user = Auth::user();

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete('fotos_perfil/' . $user->foto);
            }
            $user->foto = basename($request->file('foto')->store('fotos_perfil', 'public'));
        }

        $user->save();

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
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
