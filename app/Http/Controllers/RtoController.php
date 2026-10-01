<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Proveedor;
use App\Models\rto;
use App\Models\Camion;
use App\Models\AuditLog;
use App\Models\ElementoRto;
use App\Models\RtoDetalle;

class RtoController extends Controller
{
    public function index(Request $request)
    {
        $titulo = 'Remitos';
        $idsPermitidos = Proveedor::idsPermitidos('remitos');

        $anios = rto::selectRaw('YEAR(fechaIngresoRto) as anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        $anioActual = (int) date('Y');
        if (!$anios->contains($anioActual)) {
            $anios->prepend($anioActual);
            $anios = $anios->sortDesc()->values();
        }

        $anioSeleccionado = $request->get('anio', $anioActual);

        $items = rto::with(['proveedor'])
            ->withCount('observaciones', 'reclamos')
            ->when($idsPermitidos !== null, fn($q) => $q->whereIn('proveedores_id', $idsPermitidos))
            ->whereYear('fechaIngresoRto', $anioSeleccionado)
            ->orderBy('fechaIngresoRto', 'desc')
            ->get();
        $proveedores = Proveedor::permitidos('remitos')->where('estadoProveedor', '1')->get();
        return view('modules.rto.index', compact('titulo', 'items', 'proveedores', 'anios', 'anioSeleccionado'));
    }

    public function actualizar(Request $request, $id)
{
    try {
        $remito = rto::findOrFail($id);
        $this->autorizarProveedor($remito->proveedores_id, 'remitos');
        $datosAnteriores = $remito->toArray();

        $remito->fechaIngresoRto = $request->input('fechaIngresoRto');
        $remito->nroFacturaRto = $request->input('nroFacturaRto');
        $remito->save();

        AuditLog::registrar('remitos', 'editar', "Actualizo remito #{$remito->id} ({$remito->nroFacturaRto})", 'Rto', $remito->id, $datosAnteriores, $remito->fresh()->toArray());

        return response()->json(['success' => true, 'message' => 'Remito actualizado correctamente']);
    } catch (HttpException $e) {
        throw $e;
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

    public function store(Request $request)
    {
        $request->validate([
            'fechaIngresoRto' => 'required|date',
            'nroFacturaRto' => 'required|string|max:50',
            'idProveedor' => 'required|exists:proveedores,id',
        ]);

        $this->autorizarProveedor($request->idProveedor, 'remitos');

        $remito = DB::transaction(function () use ($request) {
            $camion = Camion::where('proveedores_id', $request->idProveedor)->lockForUpdate()->first();

            if (!$camion) {
                $camion = Camion::create([
                    'contador' => 1,
                    'proveedores_id' => $request->idProveedor,
                ]);
            }

            $remito = new Rto();
            $remito->fechaIngresoRto = $request->fechaIngresoRto;
            $remito->nroFacturaRto = $request->nroFacturaRto;
            $remito->proveedores_id = $request->idProveedor;
            $remito->camion = $camion->contador;
            $remito->save();

            $camion->increment('contador');

            return $remito;
        });
        AuditLog::registrar('remitos', 'crear', "Creo remito #{$remito->camion}", 'Rto', $remito->id, null, $remito->toArray());

        return redirect()->route('remitos.edit', $remito->id)
        ->with('success', 'Remito creado correctamente y redirigido a la edición.');
    }

    public function edit($id)
    {
        // Obtener el remito
        $items = Rto::with(['proveedor'])->findOrFail($id);

        // Validar que el remito pertenece a un proveedor permitido
        $idsPermitidos = Proveedor::idsPermitidos('remitos');
        if ($idsPermitidos !== null && !in_array($items->proveedores_id, $idsPermitidos)) {
            abort(403, 'No tiene permiso para acceder a este remito');
        }

        // Cargar los detalles del remito
        $detalles = RtoDetalle::with('elemento')
            ->where('rto_id', $id)
            ->get();

        $elementosRto = ElementoRto::all();

        // Obtener proveedores permitidos para el selector
        $proveedores = Proveedor::permitidos('remitos')->where('estadoProveedor', '1')
            ->orderBy('razonSocialProveedor')
            ->get();

        return view('modules.rto.editar', [
            'titulo' => 'Editar Remito',
            'items' => $items,
            'detalles' => $detalles,
            'proveedores' => $proveedores,
            'elementosRto' => $elementosRto
        ]);
    }

    public function pendientes(Request $request)
    {
        $titulo = 'Remitos Pendientes';
        $idsPermitidos = Proveedor::idsPermitidos('remitos');
        $proveedores = Proveedor::permitidos('remitos')->where('estadoProveedor', '1')->get();

        $anios = rto::selectRaw('YEAR(fechaIngresoRto) as anio')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->pluck('anio');

        $anioActual = (int) date('Y');
        if (!$anios->contains($anioActual)) {
            $anios->prepend($anioActual);
            $anios = $anios->sortDesc()->values();
        }

        $anioSeleccionado = $request->get('anio', $anioActual);

        $items = rto::where('estado', 'Espera')
        ->with(['proveedor'])
        ->withCount('observaciones', 'reclamos')
        ->when($idsPermitidos !== null, fn($q) => $q->whereIn('proveedores_id', $idsPermitidos))
        ->whereYear('fechaIngresoRto', $anioSeleccionado)
        ->orderBy('fechaIngresoRto', 'desc')
        ->get();

        return view('modules.rto.pendientes', compact('items', 'titulo', 'proveedores', 'anios', 'anioSeleccionado'));
    }

    public function destroy($id)
    {
        if (Gate::denies('acceso-remitos_eliminar')) {
            return response()->json(['success' => false, 'message' => 'No tiene permiso para eliminar remitos.'], 403);
        }

        try {
            $remito = rto::findOrFail($id);
            $this->autorizarProveedor($remito->proveedores_id, 'remitos');

            if ($remito->estado !== 'Espera') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden eliminar remitos en estado Espera.',
                ], 422);
            }

            $datosAnteriores = $remito->toArray();

            DB::transaction(function () use ($remito) {
                $camion = Camion::where('proveedores_id', $remito->proveedores_id)->lockForUpdate()->first();
                if ($camion && $camion->contador > 1 && (int) $remito->camion === $camion->contador - 1) {
                    $camion->decrement('contador');
                }

                $remito->delete();
            });

            AuditLog::registrar(
                'remitos', 'eliminar',
                "Elimino remito #{$remito->camion} del proveedor {$remito->proveedores_id}",
                'Rto', (int) $id, $datosAnteriores
            );

            return response()->json([
                'success' => true,
                'message' => 'Remito eliminado correctamente.',
            ]);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:Espera,Deuda,Pagado,Anulado'
        ]);

        $remito = rto::findOrFail($id);
        $this->autorizarProveedor($remito->proveedores_id, 'remitos');
        $estadoAnterior = $remito->estado;
        $remito->estado = $request->estado;
        $remito->save();

        AuditLog::registrar('remitos', 'cambiar_estado', "Cambio estado de remito #{$remito->camion} de {$estadoAnterior} a {$remito->estado}", 'Rto', $remito->id, ['estado' => $estadoAnterior], ['estado' => $remito->estado]);

        return response()->json(['success' => true]);
    }
}