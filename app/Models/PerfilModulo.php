<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilModulo extends Model
{
    protected $table = 'perfil_modulo';

    protected $fillable = [
        'perfil_id',
        'modulo',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
}
