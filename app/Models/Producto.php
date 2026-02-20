<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'proveedores_id',
        'codigo',
        'codigoBarras',
        'nombre',
        'costoDlrs',
        'TC',
        'costo',
        'modificacion',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = true;

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedores_id');
    }
}
