<?php

namespace App\Http\Controllers;

use App\Models\rto;
use App\Models\Camion;
use App\Models\Proveedor;
use App\Models\Reclamo;
use App\Models\Observacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    public function index(Request $request)
    {
        $titulo = 'Dashboard';

        $proveedores = Proveedor::where('estadoProveedor', '1')->get();

        // Años disponibles
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
        $filtrarPorAnio = $anioSeleccionado !== 'todos';

        // Métricas generales
        $queryBase = rto::query();
        if ($filtrarPorAnio) {
            $queryBase = $queryBase->whereYear('fechaIngresoRto', $anioSeleccionado);
        }

        $totalRemitos = (clone $queryBase)->count();
        $remitosEnEspera = (clone $queryBase)->where('estado', 'Espera')->count();
        $remitosConDeuda = (clone $queryBase)->where('estado', 'Deuda')->count();
        $remitosPagados = (clone $queryBase)->where('estado', 'Pagado')->count();

        // Remitos recientes
        $remitosRecientes = rto::with('proveedor')
            ->when($filtrarPorAnio, fn($q) => $q->whereYear('fechaIngresoRto', $anioSeleccionado))
            ->orderBy('fechaIngresoRto', 'desc')
            ->limit(5)
            ->get();

        // Conteos adicionales
        $totalProveedores = Proveedor::where('estadoProveedor', '1')->count();
        $totalCamiones = Camion::count();
        $totalReclamos = Reclamo::where('estadoReclamoRto', 'pendiente')->count();
        $totalObservaciones = Observacion::count();

        // Datos para el gráfico de remitos por mes
        $remitosPorMesQuery = rto::select(
            DB::raw('MONTH(fechaIngresoRto) as mes'),
            DB::raw('YEAR(fechaIngresoRto) as año'),
            DB::raw('COUNT(*) as total')
        );

        if ($filtrarPorAnio) {
            $remitosPorMesQuery->whereYear('fechaIngresoRto', $anioSeleccionado);
        } else {
            $remitosPorMesQuery->whereRaw('fechaIngresoRto >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)');
        }

        $remitosPorMes = $remitosPorMesQuery
            ->groupBy('año', 'mes')
            ->orderBy('año', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        // Formateamos los datos para el gráfico de remitos por mes
        $mesesLabels = [];
        $mesesData = [];

        foreach ($remitosPorMes as $registro) {
            $nombreMes = date('M', mktime(0, 0, 0, $registro->mes, 10));
            $mesesLabels[] = $nombreMes . ' ' . $registro->año;
            $mesesData[] = $registro->total;
        }

        // Facturación total por proveedor
        $facturacionQuery = rto::select(
            'proveedores.nombreProveedor',
            DB::raw('SUM(rto.totalFinalRto) as facturacion')
        )
            ->join('proveedores', 'rto.proveedores_id', '=', 'proveedores.id')
            ->whereNotNull('rto.totalFinalRto');

        if ($filtrarPorAnio) {
            $facturacionQuery->whereYear('rto.fechaIngresoRto', $anioSeleccionado);
        }

        $facturacionPorProveedor = $facturacionQuery
            ->groupBy('proveedores.nombreProveedor')
            ->orderBy('facturacion', 'desc')
            ->limit(5)
            ->get();

        $proveedoresLabels = $facturacionPorProveedor->pluck('nombreProveedor')->toArray();
        $proveedoresData = $facturacionPorProveedor->pluck('facturacion')->toArray();
        return view('modules.dashboard.home', compact(
            'titulo',
            'totalRemitos',
            'remitosEnEspera',
            'remitosConDeuda',
            'remitosPagados',
            'totalProveedores',
            'totalCamiones',
            'totalReclamos',
            'totalObservaciones',
            'remitosRecientes',
            'mesesLabels',
            'mesesData',
            'proveedoresLabels',
            'proveedoresData',
            'proveedores',
            'anios',
            'anioSeleccionado'
        ));
    }
}