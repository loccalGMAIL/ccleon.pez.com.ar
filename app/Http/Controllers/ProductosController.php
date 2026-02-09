<?php

namespace App\Http\Controllers;
use App\Models\AuditLog;
use App\Models\Proveedor;
use App\Models\Cotizacion;
use App\Models\Producto;
use illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ProductosController extends Controller
{
    public function index(Request $request)
    {
        $titulo = 'Productos';
        $proveedores = Proveedor::where('estadoProveedor', '1')->get();
        $cotizacion = Cotizacion::latest()->first();
        $proveedorSeleccionado = $request->query('proveedor_id');
        $productos = [];

        // Si hay un proveedor seleccionado, cargar sus productos
        if ($proveedorSeleccionado) {
            $productos = Producto::where('proveedores_id', $proveedorSeleccionado)->get();
        }
        return view('modules.productos.index', compact(
            'titulo',
            'proveedores',
            'cotizacion',
            'productos',
            'proveedorSeleccionado'
        ));
    }
    /**
     * Almacena un nuevo producto en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'proveedores_id' => 'required|exists:proveedores,id',
            'codigo' => 'nullable|string|max:255|unique:productos,codigo',
            'codigoBarras' => 'nullable|integer|unique:productos,codigoBarras',
            'nombre' => 'required|string|max:255',
            'costoDlrs' => 'required|numeric|min:0',
            'TC' => 'required|numeric|min:0',
            'costo' => 'required|numeric|min:0',
            'modificacion' => 'nullable|date',
        ]);

        try {
            // Crear el nuevo producto
            $producto = new Producto();
            $producto->proveedores_id = $request->proveedores_id;
            $producto->codigo = $request->codigo;
            $producto->codigoBarras = $request->codigoBarras;
            $producto->nombre = $request->nombre;
            $producto->costoDlrs = $request->costoDlrs;
            $producto->TC = $request->TC;
            $producto->costo = $request->costo;
            $producto->modificacion = $request->modificacion;

            $producto->save();

            AuditLog::registrar('productos', 'crear', "Creo producto {$producto->nombre}", 'Producto', $producto->id, null, $producto->toArray());

            // Redireccionar de vuelta con mensaje de éxito
            return redirect()->route('productos', ['proveedor_id' => $request->proveedores_id])
                ->with('swal_success', 'Producto agregado correctamente');
        } catch (\Exception $e) {
            // Log del error
            Log::error('Error al guardar producto: ' . $e->getMessage());

            // Redireccionar de vuelta con mensaje de error
            return redirect()->back()
                ->with('swal_error', 'Error al guardar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Obtiene los datos de un producto para edición
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            return response()->json($producto);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
    }
    /**
     * Actualiza un producto existente en la base de datos
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:255|unique:productos,codigo,' . $id,
            'codigoBarras' => 'nullable|integer|unique:productos,codigoBarras,' . $id,
            'nombre' => 'required|string|max:255',
            'costoDlrs' => 'required|numeric|min:0',
            'TC' => 'required|numeric|min:0',
            'costo' => 'required|numeric|min:0',
            'modificacion' => 'nullable|date',
        ]);

        try {
            $producto = Producto::findOrFail($id);
            $datosAnteriores = $producto->toArray();

            $producto->codigo = $request->codigo;
            $producto->codigoBarras = $request->codigoBarras;
            $producto->nombre = $request->nombre;
            $producto->costoDlrs = $request->costoDlrs;
            $producto->TC = $request->TC;
            $producto->costo = $request->costo;
            $producto->modificacion = $request->modificacion ?: now();

            $producto->save();

            AuditLog::registrar('productos', 'editar', "Edito producto {$producto->nombre}", 'Producto', $producto->id, $datosAnteriores, $producto->fresh()->toArray());

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Producto actualizado correctamente',
                    'producto' => $producto
                ]);
            }

            // Redirigir a la vista del proveedor seleccionado
            return redirect()->route('productos', ['proveedor_id' => $producto->proveedores_id])
                ->with('swal_success', 'Producto actualizado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar producto: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Error al actualizar el producto: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Error al actualizar el producto']);
        }
    }
    /**
     * Elimina un producto de la base de datos
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $datosAnteriores = $producto->toArray();
            $nombre = $producto->nombre;
            $proveedorId = $producto->proveedores_id;

            $producto->delete();

            AuditLog::registrar('productos', 'eliminar', "Elimino producto {$nombre}", 'Producto', (int) $id, $datosAnteriores);

            // Redirigir a la vista del proveedor seleccionado
            return redirect()->route('productos', ['proveedor_id' => $producto->proveedores_id])
                ->with('swal_success', 'Producto borrado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar producto: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'Error al borrar el producto']);

        }
    }

    // public function guardarCotizacion(Request $request)
    // {
    //     // Validar el valor recibido
    //     $request->validate([
    //         'cotizacion' => 'required|numeric|min:0',
    //     ]);

    //     // Buscar la cotización existente o crear una nueva
    //     $cotizacion = Cotizacion::first();
    //     if (!$cotizacion) {
    //         $cotizacion = new Cotizacion();
    //     }

    //     $cotizacion->precioDolar = $request->cotizacion;
    //     $cotizacion->save();

    //     // Configurar SweetAlert para la confirmación
    //     return redirect()->back()->with('swal_success', 'Cotización del dólar actualizada correctamente');
    // }


    public function guardarCotizacion(Request $request)
    {
        // Validar el valor recibido
        $request->validate([
            'cotizacion' => 'required|numeric|min:0',
        ]);
        
        try {
            // Comenzar una transacción de base de datos
            DB::beginTransaction();
            
            // Buscar la cotización existente o crear una nueva
            $cotizacion = Cotizacion::first();
            if (!$cotizacion) {
                $cotizacion = new Cotizacion();
            }
            
            // Guardar el nuevo valor de cotización
            $cotizacion->precioDolar = $request->cotizacion;
            $cotizacion->save();
            
            // Actualizar el TC y recalcular el costo en todos los productos
            $productos = Producto::all();
            foreach ($productos as $producto) {
                $producto->TC = $request->cotizacion;
                $producto->costo = $producto->costoDlrs * $request->cotizacion;
                $producto->save();
            }
            
            // Confirmar la transacción
            DB::commit();

            AuditLog::registrar('productos', 'actualizar_cotizacion', "Actualizo cotizacion del dolar a {$request->cotizacion}", 'Cotizacion', $cotizacion->id, null, ['precioDolar' => $cotizacion->precioDolar]);

            // Configurar SweetAlert para la confirmación
            return redirect()->back()->with('swal_success', 'Cotización del dólar actualizada correctamente y precios recalculados');
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            DB::rollback();
            
            // Log del error
            Log::error('Error al actualizar cotización y productos: ' . $e->getMessage());
            
            // Retornar con mensaje de error
            return redirect()->back()->with('swal_error', 'Error al actualizar la cotización: ' . $e->getMessage());
        }
    }



    public function actualizarCotizacionExterna(Request $request)
    {
        try {
            // Obtener la cotización real del Banco Nación Argentina
            $nuevaCotizacion = $this->obtenerCotizacionBancoNacion();

            // Buscar la cotización existente o crear una nueva
            $cotizacion = Cotizacion::first();
            if (!$cotizacion) {
                $cotizacion = new Cotizacion();
            }

            // Actualizar el valor
            $cotizacion->precioDolar = $nuevaCotizacion;
            $cotizacion->save();

            AuditLog::registrar('productos', 'actualizar_cotizacion', "Actualizo cotizacion externa a {$nuevaCotizacion} (Banco Nacion)", 'Cotizacion', $cotizacion->id, null, ['precioDolar' => $nuevaCotizacion]);

            return redirect()->back()->with('swal_success', "Cotización actualizada a $nuevaCotizacion (Banco Nación)");
        } catch (\Exception $e) {
            Log::error('Error al actualizar cotización externa: ' . $e->getMessage());
            return redirect()->back()->with('swal_error', 'Error al actualizar la cotización: ' . $e->getMessage());
        }
    }

    /**
     * @return float La cotización del dólar oficial (venta)
     * @throws \Exception Si hay algún error en la consulta
     */
    private function obtenerCotizacionBancoNacion()
    {
        // Configuración de la petición
        $opciones = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept: application/json'
                ]
            ]
        ];

        // Crear el contexto para la petición
        $contexto = stream_context_create($opciones);

        // Realizar la petición a la API
        $respuesta = @file_get_contents('https://dolarapi.com/v1/cotizaciones/oficial', false, $contexto);

        // Verificar si la petición fue exitosa
        if ($respuesta === false) {
            // Si falla, intentamos con el método alternativo
            return $this->obtenerCotizacionBancoNacionAlternativa();
        }

        // Decodificar la respuesta JSON
        $data = json_decode($respuesta, true);

        // Verificar que la respuesta contenga los datos esperados
        if (!isset($data['venta'])) {
            throw new \Exception('Formato de respuesta inesperado: no se encontró el valor de venta');
        }

        // Retornar el valor de venta
        return (float) $data['venta'];
    }

    private function obtenerCotizacionBancoNacionAlternativa()
    {
        // Configuración de la petición
        $opciones = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ]
            ]
        ];

        // Crear el contexto para la petición
        $contexto = stream_context_create($opciones);

        // Realizar la petición a la página del Banco Nación
        $html = @file_get_contents('https://www.bna.com.ar/Personas', false, $contexto);

        if ($html === false) {
            throw new \Exception('No se pudo acceder a la página del Banco Nación');
        }

        // Buscamos la tabla de cotizaciones
        preg_match('/<table class="table cotizacion">(.*?)<\/table>/s', $html, $matches);

        if (empty($matches)) {
            throw new \Exception('No se pudo encontrar la tabla de cotizaciones en la página');
        }

        // Buscamos específicamente la fila del dólar y el valor de venta
        preg_match('/Dolar U\.S\.A.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>/s', $matches[1], $cotizacion);

        if (empty($cotizacion) || !isset($cotizacion[2])) {
            throw new \Exception('No se pudo encontrar la cotización del dólar en la tabla');
        }

        // Limpiamos y convertimos el valor a float
        $valorVenta = str_replace(',', '.', trim(strip_tags($cotizacion[2])));

        return (float) $valorVenta;
    }
}
