<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Checkrol
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $modulo)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Cortar la sesion si el usuario fue desactivado mientras estaba conectado
        if (!Auth::user()->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Tu cuenta está inactiva.'], 401);
            }

            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta está inactiva!']);
        }

        // Verificar si el usuario tiene acceso al modulo
        if (!Auth::user()->tieneAcceso($modulo)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
