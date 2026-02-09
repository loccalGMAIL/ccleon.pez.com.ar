<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'modulo',
        'accion',
        'modelo',
        'registro_id',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip',
        'created_at',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function registrar(
        string $modulo,
        string $accion,
        string $descripcion,
        ?string $modelo = null,
        ?int $registroId = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ): self {
        $user = Auth::user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Sistema',
            'modulo' => $modulo,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'modelo' => $modelo,
            'registro_id' => $registroId,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'ip' => Request::ip(),
            'created_at' => now(),
        ]);
    }
}
