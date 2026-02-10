<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilProveedor extends Model
{
    protected $table = 'perfil_proveedor';

    protected $fillable = [
        'perfil_id',
        'proveedores_id',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedores_id');
    }
}
