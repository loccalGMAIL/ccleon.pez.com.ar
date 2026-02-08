<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gates dinamicos por modulo
        $modulos = config('modulos', []);
        foreach ($modulos as $clave => $modulo) {
            Gate::define("acceso-{$clave}", function (User $user) use ($clave) {
                return $user->tieneAcceso($clave);
            });
        }
    }
}
