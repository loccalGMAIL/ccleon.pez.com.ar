<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Observacion;
use App\Models\rto;

class Observaciones extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titulo = 'Observaciones';
        $items = Observacion::with('rto')
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
        $items = Observacion::with('proveedor')
            ->where('Rto_id', $id)
            ->get();

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

        $observacion = Observacion::findOrFail($id);
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
            $reclamo = Observacion::findOrFail($id);
            $datosAnteriores = $reclamo->toArray();
            $reclamo->delete();

            AuditLog::registrar('observaciones', 'eliminar', "Elimino observacion #{$id}", 'Observacion', (int) $id, $datosAnteriores);

            return response()->json(['success' => true, 'message' => 'Reclamo eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el reclamo: ' . $e->getMessage()]);
        }
    }
}
