<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Observacion;
use App\Models\rto;
use App\Models\Proveedor;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Observaciones extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titulo = 'Observaciones';
        $idsPermitidos = Proveedor::idsPermitidos('remitos');
        $items = Observacion::with('rto.proveedor')
            ->whereHas('rto', fn ($q) => $q->when($idsPermitidos !== null, fn ($r) => $r->whereIn('proveedores_id', $idsPermitidos)))
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('modules.rto.observaciones.index', compact('titulo', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Rto_id' => 'required|exists:rto,id',
            'descripcionObservacionesRto' => 'required|string',
        ]);

        $this->autorizarProveedor(rto::findOrFail($request->Rto_id)->proveedores_id, 'remitos');

        $observacion = Observacion::create([
            'Rto_id' => $request->Rto_id,
            'descripcionObservacionesRto' => $request->descripcionObservacionesRto,
            'created_at' => now(),
        ]);

        AuditLog::registrar('observaciones', 'crear', "Creo observacion en remito #{$request->Rto_id}", 'Observacion', $observacion->id, null, $observacion->toArray());

        return redirect()->back()->with('success', 'Observación agregada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $remito = rto::with('proveedor')->findOrFail($id);
        $this->autorizarProveedor($remito->proveedores_id, 'remitos');
        $items = Observacion::where('Rto_id', $id)->get();

        return view('modules.rto.observaciones.index', [
            'items' => $items,
            'remito' => $remito,
            'titulo' => 'Observaciones del Remito',
            'singleRemito' => true // Bandera para indicar que estamos viendo un solo remito
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'descripcionObservacionesRto' => 'required|string',
        ]);

        $observacion = Observacion::with('rto')->findOrFail($id);
        $this->autorizarProveedor($observacion->rto?->proveedores_id, 'remitos');
        $datosAnteriores = $observacion->toArray();

        $observacion->update([
            'descripcionObservacionesRto' => $request->descripcionObservacionesRto,
            'updated_at' => now(),
        ]);

        AuditLog::registrar('observaciones', 'editar', "Edito observacion #{$id}", 'Observacion', (int) $id, $datosAnteriores, $observacion->fresh()->toArray());

        return redirect()->back()->with('success', 'Observación actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     $observacion = Observacion::findOrFail($id);
    //     $observacion->delete();

    //     return redirect()->back()->with('success', 'Observación eliminada correctamente');
    // }

    public function destroy($id)
    {
        try {
            $observacion = Observacion::with('rto')->findOrFail($id);
            $this->autorizarProveedor($observacion->rto?->proveedores_id, 'remitos');
            $datosAnteriores = $observacion->toArray();
            $observacion->delete();

            AuditLog::registrar('observaciones', 'eliminar', "Elimino observacion #{$id}", 'Observacion', (int) $id, $datosAnteriores);

            return response()->json(['success' => true, 'message' => 'Observacion eliminada correctamente']);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la observacion: ' . $e->getMessage()]);
        }
    }
}
