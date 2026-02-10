<?php

namespace App\Http\Controllers;

use App\Models\Logistica;
use App\Models\Proveedor;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LogisticaController extends Controller
{
    public function index()
    {
        $idsPermitidos = Proveedor::idsPermitidos('logistica');

        $items = Logistica::with('proveedor')
            ->when($idsPermitidos !== null, fn($q) => $q->whereIn('proveedores_id', $idsPermitidos))
            ->orderBy('fecha_pedido', 'desc')->get();
        $proveedores = Proveedor::permitidos('logistica')->where('estadoProveedor', 1)->orderBy('razonSocialProveedor')->get();
        $titulo = 'Logistica';

        return view('modules.logistica.index', compact('items', 'proveedores', 'titulo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedores_id' => 'required|exists:proveedores,id',
            'fecha_pedido' => 'required|date',
            'etd' => 'nullable|date',
            'eta' => 'nullable|date',
            'destino' => 'nullable|string|max:200',
            'transporte' => 'nullable|string|max:200',
            'observaciones' => 'nullable|string',
        ]);

        $logistica = Logistica::create([
            'proveedores_id' => $request->proveedores_id,
            'fecha_pedido' => $request->fecha_pedido,
            'etd' => $request->etd,
            'eta' => $request->eta,
            'destino' => $request->destino,
            'transporte' => $request->transporte,
            'observaciones' => $request->observaciones,
            'estado' => 'Pendiente',
        ]);

        AuditLog::registrar('logistica', 'crear', "Creo registro de logistica #{$logistica->id}", 'Logistica', $logistica->id, null, $logistica->toArray());

        return redirect()->route('logistica')->with('swal_success', 'Registro de logistica creado correctamente');
    }

    public function actualizarCampo(Request $request)
    {
        try {
            $id = $request->input('id');
            $field = $request->input('field');
            $value = $request->input('value');

            $camposPermitidos = [
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

            if (!in_array($field, $camposPermitidos)) {
                return response()->json(['success' => false, 'message' => 'Campo no valido']);
            }

            $logistica = Logistica::findOrFail($id);
            $antes = [$field => $logistica->$field];

            // Para campos de fecha, convertir string vacio a null
            if (in_array($field, ['fecha_pedido', 'etd', 'eta', 'arribo_confirmado']) && $value === '') {
                $value = null;
            }

            // Para proveedores_id, validar que exista y que el usuario tenga permiso
            if ($field === 'proveedores_id') {
                $proveedor = Proveedor::find($value);
                if (!$proveedor) {
                    return response()->json(['success' => false, 'message' => 'Proveedor no valido']);
                }
                $idsPermitidos = Proveedor::idsPermitidos('logistica');
                if ($idsPermitidos !== null && !in_array((int)$value, $idsPermitidos)) {
                    return response()->json(['success' => false, 'message' => 'No tiene permiso para este proveedor']);
                }
            }

            // Para estado, validar que sea un valor permitido
            if ($field === 'estado') {
                $estadosPermitidos = ['Pendiente', 'En transito', 'Arribado', 'Demorado', 'Cerrado'];
                if (!in_array($value, $estadosPermitidos)) {
                    return response()->json(['success' => false, 'message' => 'Estado no valido']);
                }
            }

            $logistica->$field = $value;
            $logistica->save();

            $despues = [$field => $logistica->$field];

            AuditLog::registrar('logistica', 'editar', "Actualizo campo {$field} en logistica #{$id}", 'Logistica', (int) $id, $antes, $despues);

            // Para proveedor, devolver tambien el nombre
            $extra = [];
            if ($field === 'proveedores_id') {
                $logistica->load('proveedor');
                $extra['displayValue'] = $logistica->proveedor->razonSocialProveedor ?? '';
            }

            return response()->json(array_merge([
                'success' => true,
                'value' => $logistica->$field,
            ], $extra));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $logistica = Logistica::findOrFail($id);
            $datosAnteriores = $logistica->toArray();

            $logistica->delete();

            AuditLog::registrar('logistica', 'eliminar', "Elimino registro de logistica #{$id}", 'Logistica', (int) $id, $datosAnteriores);

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
