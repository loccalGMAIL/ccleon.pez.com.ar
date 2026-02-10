<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
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
        $datosAnteriores = $remito->toArray();

        $remito->fechaIngresoRto = $request->input('fechaIngresoRto');
        $remito->nroFacturaRto = $request->input('nroFacturaRto');
        $remito->save();

        AuditLog::registrar('remitos', 'editar', "Actualizo remito #{$remito->camion}", 'Rto', $remito->id, $datosAnteriores, $remito->fresh()->toArray());

        return response()->json(['success' => true, 'message' => 'Remito actualizado correctamente']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

    public function create()
    {
        $titulo = 'Crear Remito';
        $proveedores = Proveedor::permitidos('remitos')->where('estadoProveedor', '1')
            ->orderBy('razonSocialProveedor')
            ->get();
        return view('modules.rto.create', compact('titulo', 'proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fechaIngresoRto' => 'required|date',
            'nroFacturaRto' => 'required|string|max:50',
            'idProveedor' => 'required|exists:proveedores,id',
        ]);

        // Buscar el camión para este proveedor
        $camion = Camion::where('proveedores_id', $request->idProveedor)->first();

        // Si no existe un camión para este proveedor, creamos uno con contador inicial
        if (!$camion) {
            $camion = new Camion();
            $camion->contador = 1;
            $camion->proveedores_id = $request->idProveedor;
            $camion->save();
        }

        // Crear el remito usando el contador del camión como número de camión
        $remito = new Rto();
        $remito->fechaIngresoRto = $request->fechaIngresoRto;
        $remito->nroFacturaRto = $request->nroFacturaRto;
        $remito->proveedores_id = $request->idProveedor;
        $remito->camion = $camion->contador; // Usar el contador como número de camión

        // Incrementar el contador para el próximo remito
        $camion->contador += 1;
        $camion->save();

        // Guardar el remito
        $remito->save();

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'fechaIngresoRto' => 'required|date',
            'nroFacturaRto' => 'required|string|max:50',
            'idProveedor' => 'required|exists:proveedores,id',
        ]);

        $remito = Rto::findOrFail($id);

        // Actualizar datos básicos del remito
        $remito->fechaIngresoRto = $request->fechaIngresoRto;
        $remito->nroFacturaRto = $request->nroFacturaRto;

        // Si cambia el proveedor, manejamos la lógica del camión
        if ($remito->proveedores_id != $request->idProveedor) {
            $remito->proveedores_id = $request->idProveedor;

            // Buscar el camión para el nuevo proveedor
            $camion = Camion::where('proveedores_id', $request->idProveedor)->first();

            // Si no existe un camión para este proveedor, creamos uno
            if (!$camion) {
                $camion = new Camion();
                $camion->contador = 1;
                $camion->proveedores_id = $request->idProveedor;
                $camion->save();
            }

            // Usar el contador actual como número de camión
            $remito->camion = $camion->contador;

            // Incrementar el contador para el próximo remito
            $camion->contador += 1;
            $camion->save();
        }

        $remito->save();

        AuditLog::registrar('remitos', 'editar', "Edito remito #{$remito->camion}", 'Rto', $remito->id, null, $remito->fresh()->toArray());

        return redirect()->route('remitos.edit', $id)
            ->with('success', 'Remito actualizado correctamente');
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

    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:Espera,Deuda,Pagado,Anulado'
        ]);

        $remito = rto::findOrFail($id);
        $estadoAnterior = $remito->estado;
        $remito->estado = $request->estado;
        $remito->save();

        AuditLog::registrar('remitos', 'cambiar_estado', "Cambio estado de remito #{$remito->camion} de {$estadoAnterior} a {$remito->estado}", 'Rto', $remito->id, ['estado' => $estadoAnterior], ['estado' => $remito->estado]);

        return response()->json(['success' => true]);
    }
}