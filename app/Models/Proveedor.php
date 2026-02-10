<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Proveedor extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombreProveedor',
        'dniProveedor',
        'razonSocialProveedor',
        'cuitProveedor',
        'telefonoProveedor',
        'mailProveedor',
        'direccionProveedor',
        'estadoProveedor'
    ];
    public $timestamps = true;
    protected $softDelete = true;
    
    public function camiones()
    {
        return $this->hasMany(Camion::class, 'proveedores_id', 'id');
    }

    /**
     * Scope: filtra proveedores segun restriccion del perfil del usuario autenticado.
     * Si no hay restriccion para el modulo dado, retorna query sin filtro.
     */
    public function scopePermitidos($query, string $modulo)
    {
        $ids = static::idsPermitidos($modulo);

        if ($ids === null) {
            return $query;
        }

        return $query->whereIn('id', $ids);
    }

    /**
     * Retorna array de IDs de proveedores permitidos para el modulo,
     * o null si no hay restriccion (el usuario ve todo).
     */
    public static function idsPermitidos(string $modulo): ?array
    {
        $user = Auth::user();
        if (!$user || !$user->perfil_id) {
            return null;
        }

        $perfil = Perfil::with(['proveedoresPermitidos', 'modulosRestringidos'])
            ->find($user->perfil_id);

        if (!$perfil) {
            return null;
        }

        // Si no hay proveedores asignados al perfil → sin restriccion
        if ($perfil->proveedoresPermitidos->isEmpty()) {
            return null;
        }

        // Si el modulo no esta en la lista de modulos restringidos → sin restriccion
        if (!$perfil->modulosRestringidos->contains('modulo', $modulo)) {
            return null;
        }

        return $perfil->proveedoresPermitidos->pluck('proveedores_id')->toArray();
    }
}
