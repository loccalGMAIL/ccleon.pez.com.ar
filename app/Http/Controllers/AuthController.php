<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function index()
    {
        $titulo = 'Login de usuarios';        
        return view('modules.auth.login', compact('titulo'));
    }
    
    public function loguear(Request $request){
        //validar datos de las credenciales
        $credenciales = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //buscar mail
        $user = User::where('email',$request->email)->first();

        //validar usuario y contraseña
        if(!$user||!Hash::check($request->password, $user->password)){
            return back()->withErrors(['email' => 'Credencial incorrecta!'])->withInput();
        }

        //Ver si el usuario esta activo
        if(!$user->activo){
            return back()->withErrors(['email' => 'Tu cuenta está inactiva!']);
        }

        //crear sesion de usuario
        Auth::login($user);
        $request->session()->regenerate();

        AuditLog::registrar('usuarios', 'login', "Inicio sesion");

        return to_route('home');
    }

    public function logout(){
        AuditLog::registrar('usuarios', 'logout', "Cerro sesion");
        Auth::logout();
        return to_route('login');
    }

    public function crearAdmin(){
        User::create(
        [
            'name' => 'Claudio',
            'email' => 'c@c.com',
            'password' => Hash::make('12345'),
            'activo' => true,
            'rol'=> 'admin' 
        ]);

        return "Admin creado con exito!!!";
    }


}


