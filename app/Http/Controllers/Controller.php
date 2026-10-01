<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;

abstract class Controller
{
    /**
     * Indica si el usuario autenticado puede operar sobre el proveedor en el modulo dado.
     */
    protected function proveedorPermitido(int|string|null $proveedorId, string $modulo): bool
    {
        $idsPermitidos = Proveedor::idsPermitidos($modulo);

        return $idsPermitidos === null || in_array((int) $proveedorId, $idsPermitidos, true);
    }

    /**
     * Corta la peticion con 403 si el proveedor esta fuera de la restriccion del perfil.
     */
    protected function autorizarProveedor(int|string|null $proveedorId, string $modulo): void
    {
        if (!$this->proveedorPermitido($proveedorId, $modulo)) {
            abort(403, 'No tiene permiso para operar con este proveedor.');
        }
    }
}
