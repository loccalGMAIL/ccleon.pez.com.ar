<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    protected $table = 'perfiles';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function modulos()
    {
        return $this->hasMany(PerfilModulo::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function tieneModulo(string $modulo): bool
    {
        return $this->modulos->contains('modulo', $modulo);
    }
}
