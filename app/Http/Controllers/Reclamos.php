<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
Use App\Models\Reclamo;
use App\Models\rto;

class Reclamos extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($rtoId = null)
    {
        $titulo = 'Reclamos';
        $items = Reclamo::all();
        // return view('modules.rto.reclamos.index', compact('titulo', 'items'));

            $query = Reclamo::with('rto');
            
            if ($rtoId) {
                $query->where('Rto_id', $rtoId);
            }
            
            $reclamos = $query->get();
            
            return view('modules.rto.reclamos.index', compact('titulo','reclamos', 'rtoId', 'items'));
        
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'Rto_id' => 'required|exists:rto,id',
            'producto' => 'required|string|max:255',
            'cantidad' => 'required|numeric',
            'observaciones' => 'required|string',
            'estadoReclamoRto' => 'required|in:pendiente,resuelto',
            'resolucionReclamoRto' => 'nullable|required_if:estadoReclamoRto,resuelto|string',
        ]);

        $reclamo = Reclamo::create([
            'Rto_id' => $request->Rto_id,
            'producto' => $request->producto,
            'cantidad' => $request->cantidad,
            'observaciones' => $request->observaciones,
            'estadoReclamoRto' => $request->estadoReclamoRto,
            'resolucionReclamoRto' => $request->resolucionReclamoRto,
        ]);

        AuditLog::registrar('reclamos', 'crear', "Creo reclamo en remito #{$request->Rto_id}", 'Reclamo', $reclamo->id, null, $reclamo->toArray());

        return redirect()->back()->with('success', 'Reclamo registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $remito = Rto::with('proveedor')->findOrFail($id);
        $items = Reclamo::with('proveedor')
            ->where('Rto_id', $id)
            ->get();
        
        return view('modules.rto.reclamos.index', [
            'items' => $items,
            'remito' => $remito,
            'titulo' => 'Reclamos del Remito',
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

    /* Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {
       $request->validate([
           'producto' => 'required|string|max:255',
           'cantidad' => 'required|numeric',
           'observaciones' => 'required|string',
           'estadoReclamoRto' => 'required|in:pendiente,resuelto',
           'resolucionReclamoRto' => 'nullable|required_if:estadoReclamoRto,resuelto|string',
       ]);

       $reclamo = Reclamo::findOrFail($id);
       $datosAnteriores = $reclamo->toArray();
       $reclamo->update($request->all());

       AuditLog::registrar('reclamos', 'editar', "Edito reclamo #{$id}", 'Reclamo', (int) $id, $datosAnteriores, $reclamo->fresh()->toArray());

       return redirect()->back()->with('success', 'Reclamo actualizado correctamente');
   }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $reclamo = Reclamo::findOrFail($id);
            $datosAnteriores = $reclamo->toArray();
            $reclamo->delete();

            AuditLog::registrar('reclamos', 'eliminar', "Elimino reclamo #{$id}", 'Reclamo', (int) $id, $datosAnteriores);

            return response()->json(['success' => true, 'message' => 'Reclamo eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el reclamo: ' . $e->getMessage()]);
        }
    }
}
