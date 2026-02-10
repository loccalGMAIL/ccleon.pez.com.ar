<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilRestriccionModulo extends Model
{
    protected $table = 'perfil_restriccion_modulo';

    protected $fillable = [
        'perfil_id',
        'modulo',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
}
