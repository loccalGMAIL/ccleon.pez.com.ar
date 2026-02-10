<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logistica extends Model
{
    use SoftDeletes;

    protected $table = 'logistica';

    protected $fillable = [
        'proveedores_id',
        'fecha_pedido',
        'etd',
        'eta',
        'destino',
        'transporte',
        'arribo_confirmado',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'etd' => 'date',
        'eta' => 'date',
        'arribo_confirmado' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedores_id');
    }
}
