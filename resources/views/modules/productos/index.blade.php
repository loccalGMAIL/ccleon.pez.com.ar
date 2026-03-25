@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Productos</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Productos</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Listado de Productos por Proveedor</h5>
                            <p class="card-text">Para comenzar a operar, seleccione un proveedor.</p>
                            {{-- Menu de botones --}}
                            <!-- Div de los botones y controles principales -->
                            <div class="d-flex justify-content-between mt-3 mb-1">
                                <!-- Bloque izquierdo con select y botones -->
                                <div class="d-flex gap-2">
                                    <select class="form-select" id="idProveedor" name="idProveedor" style="width: auto">
                                        <option value="">Seleccionar proveedor</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{ $proveedor->id }}">
                                                {{ $proveedor->razonSocialProveedor }} ({{ $proveedor->nombreProveedor }})
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="button" id="toggleFinalColumns" class="btn btn-sm btn-primary" disabled>
                                        <i class="fa-solid fa-eye"></i> Mostrar Columna Costos
                                    </button>
                                    <a href="#" class="btn btn-sm btn-primary disabled" data-bs-toggle="modal"
                                        data-bs-target="#agregarRemitoModal">
                                        <i class="fa-solid fa-circle-plus"></i> Agregar nuevo producto
                                    </a>
                                </div>

                                <!-- Bloque derecho solo con el formulario de cotización -->
                                <div>
                                    <form action="{{ route('cotizacion.guardar') }}" method="POST">
                                        @csrf
                                        <div class="input-group" style="width: auto;">
                                            <span class="input-group-text bg-primary text-white fw-bold">USD</span>
                                            <input type="number" name="cotizacion" value="{{ $cotizacion->precioDolar }}"
                                                step="0.01"
                                                class="form-control text-center fw-bold bg-primary text-white"
                                                placeholder="0.00" style="width: 6rem;">
                                            <button type="submit" name="action" value="guardar" class="btn btn-success"
                                                title="Guardar cotización">
                                                <i class="fa-solid fa-save"></i>
                                            </button>
                                            <button type="submit" formaction="{{ route('cotizacion.actualizar-externa') }}"
                                                class="btn btn-primary" title="Actualizar cotización desde API">
                                                <i class="fa-solid fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Segundo div con texto de actualización alineado con el componente de cotización -->
                            <div class="d-flex justify-content-between mb-3">
                                <div></div> <!-- Div vacío en el lado izquierdo para mantener la estructura -->
                                <small class="text-muted" style="font-size: 0.75rem; margin-right: 10px;">
                                    Última actualización:
                                    @if (isset($cotizacion))
                                        {{ $cotizacion->updated_at->format('d/m/Y H:i') }}
                                    @else
                                        No disponible
                                    @endif
                                </small>
                            </div>

                            <!-- Table with stripped rows -->
                            <div class="table-responsive mt-4">
                                <table id="productosTable" class="table datatable">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Código Barras</th>
                                            <th>Nombre</th>
                                            <th class="costos-column d-none">USD</th>
                                            <th class="costos-column d-none">TC</th>
                                            <th>ARS</th>
                                            <th>Modificado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (isset($productos) && count($productos) > 0)
                                            @foreach ($productos as $producto)
                                                <tr>
                                                    <td>{{ $producto->codigo }}</td>
                                                    <td>{{ $producto->codigoBarras }}</td>
                                                    <td>{{ $producto->nombre }}</td>
                                                    <td class="costos-column d-none">
                                                        {{ number_format($producto->costoDlrs, 4) }}</td>
                                                    <td class="costos-column d-none">{{ number_format($producto->TC, 2) }}
                                                    </td>
                                                    <td>$ {{ number_format($producto->costo, 2) }}</td>
                                                    <td>{{ $producto->modificacion ? date('d/m/Y', strtotime($producto->modificacion)) : date('d/m/Y', strtotime($producto->updated_at)) }}
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <button class="btn btn-success btn-sm"
                                                                onclick="editarProducto({{ $producto->id }})">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm"
                                                                onclick="eliminarProducto({{ $producto->id }})">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- End Table with stripped rows -->

                        </div>
                    </div>

                </div>
                <!-- End Table with stripped rows -->
            </div>

            <!-- Modal para agregar nuevo producto -->
            <div class="modal fade" id="agregarRemitoModal" tabindex="-1" aria-labelledby="agregarRemitoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="agregarRemitoModalLabel">Agregar Nuevo Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <form action="{{ route('productos.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <!-- Campo oculto para el ID del proveedor -->
                                <input type="hidden" name="proveedores_id" id="modal_proveedor_id">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="codigo" class="form-label">Código</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo"
                                            placeholder="Código del producto">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="codigoBarras" class="form-label">Código de Barras</label>
                                        <input type="number" class="form-control" id="codigoBarras" name="codigoBarras"
                                            placeholder="Código de barras">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre del Producto <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                        placeholder="Nombre del producto" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="costoDlrs" class="form-label">Costo en USD <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.0001" class="form-control" id="costoDlrs"
                                            name="costoDlrs" required placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="TC" class="form-label">Tipo de Cambio</label>
                                        <input type="number" class="form-control" id="TC"
                                            value="{{ $cotizacion->precioDolar ?? 0 }}" name="TC"
                                            placeholder="0.00" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="costo" class="form-label">Costo en ARS</label>
                                        <input type="number" step="0.01" class="form-control" id="costo"
                                            name="costo" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="modificacion" class="form-label">Fecha de Modificación</label>
                                    <input type="date" class="form-control" id="modificacion" name="modificacion"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal para editar producto -->
            <div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="editarProductoModalLabel">Editar Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <form id="editarProductoForm" action="" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="edit_codigo" class="form-label">Código</label>
                                        <input type="text" class="form-control" id="edit_codigo" name="codigo"
                                            placeholder="Código del producto">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_codigoBarras" class="form-label">Código de Barras</label>
                                        <input type="number" class="form-control" id="edit_codigoBarras"
                                            name="codigoBarras" placeholder="Código de barras">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_nombre" class="form-label">Nombre del Producto <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nombre" name="nombre"
                                        placeholder="Nombre del producto" required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="edit_costoDlrs" class="form-label">Costo en USD <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.0001" class="form-control" id="edit_costoDlrs"
                                            name="costoDlrs" required placeholder="0.00">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_TC" class="form-label">Tipo de Cambio</label>
                                        <input type="number" step="0.0001" class="form-control" id="edit_TC"
                                            name="TC" placeholder="0.00" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="edit_costo" class="form-label">Costo en ARS</label>
                                        <input type="number" step="0.01" class="form-control" id="edit_costo"
                                            name="costo" placeholder="0.00" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_modificacion" class="form-label">Fecha de Modificación</label>
                                    <input type="date" class="form-control" id="edit_modificacion"
                                        name="modificacion" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning">Actualizar Producto</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Para mensajes de éxito
            @if (session('swal_success'))
                Swal.fire({
                    title: '',
                    text: "{{ session('swal_success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            // Para mensajes de error
            @if (session('swal_error'))
                Swal.fire({
                    title: 'Error',
                    text: "{{ session('swal_error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // Evento de cambio del select de proveedores
            $('#idProveedor').change(function() {
                let proveedorId = $(this).val();

                if (proveedorId) {
                    // Habilitar los botones que estaban desactivados
                    $('#toggleFinalColumns').prop('disabled', false);
                    $('a[data-bs-target="#agregarRemitoModal"]').removeClass('disabled');

                    // Redirigir a la misma página con el ID del proveedor
                    window.location.href = `/productos?proveedor_id=${proveedorId}`;
                } else {
                    // Deshabilitar los botones
                    $('#toggleFinalColumns').prop('disabled', true);
                    $('a[data-bs-target="#agregarRemitoModal"]').addClass('disabled');
                }
            });

            // Botón para mostrar/ocultar columnas de costos
            $('#toggleFinalColumns').click(function() {
                $('.costos-column').toggleClass('d-none');

                // Cambiar el texto del botón según el estado
                if ($('.costos-column').hasClass('d-none')) {
                    $(this).html('<i class="fa-solid fa-eye"></i> Mostrar Columna Costos');
                } else {
                    $(this).html('<i class="fa-solid fa-eye-slash"></i> Ocultar Columna Costos');
                }
            });

            // Comprobar si hay un proveedor seleccionado al cargar la página
            @if (isset($proveedorSeleccionado) && $proveedorSeleccionado)
                $('#idProveedor').val('{{ $proveedorSeleccionado }}');
                $('#toggleFinalColumns').prop('disabled', false);
                $('a[data-bs-target="#agregarRemitoModal"]').removeClass('disabled');
            @endif
        });

        // Script para el modal de agregar producto
        $('a[data-bs-target="#agregarRemitoModal"]').click(function() {
            // Obtener el ID del proveedor seleccionado
            let proveedorId = $('#idProveedor').val();
            // Establecer el tipo de cambio actual
            let tipoCambio = {{ $cotizacion->precioDolar ?? 0 }};
            $('#TC').val(tipoCambio);

            // Si no hay proveedor seleccionado, mostrar alerta y evitar abrir el modal
            if (!proveedorId) {
                Swal.fire({
                    title: 'Error',
                    text: 'Debe seleccionar un proveedor primero',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Establecer el ID del proveedor en el campo oculto
            $('#modal_proveedor_id').val(proveedorId);


            // Limpiar otros campos del formulario
            $('#codigo').val('');
            $('#codigoBarras').val('');
            $('#nombre').val('');
            $('#costoDlrs').val('');
            $('#costo').val('');
            $('#modificacion').val('{{ date('Y-m-d') }}');
        });

        // Calcular automáticamente el costo en pesos cuando cambia el costo en dólares
        $('#costoDlrs').on('input', function() {
            let costoDlrs = parseFloat($(this).val()) || 0;
            let tipoCambio = parseFloat($('#TC').val()) || 0;
            let costoArs = costoDlrs * tipoCambio;

            // Establecer el costo en pesos con 2 decimales
            $('#costo').val(costoArs.toFixed(2));
        });

        // Función para editar producto
        window.editarProducto = function(id) {
            // Obtener datos del producto mediante AJAX
            $.ajax({
                url: `{{ url('/') }}/productos/${id}/edit`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Establecer la acción del formulario
                    $('#editarProductoForm').attr('action', `/productos/update/${id}`);

                    // Llenar el formulario con los datos del producto
                    $('#edit_codigo').val(data.codigo);
                    $('#edit_codigoBarras').val(data.codigoBarras);
                    $('#edit_nombre').val(data.nombre);
                    $('#edit_costoDlrs').val(data.costoDlrs);
                    $('#edit_TC').val(data.TC);
                    $('#edit_costo').val(data.costo);

                    // Formatear la fecha para el input date
                    if (data.modificacion) {
                        let fecha = new Date(data.modificacion);
                        let fechaFormateada = fecha.toISOString().split('T')[0];
                        $('#edit_modificacion').val(fechaFormateada);
                    } else {
                        $('#edit_modificacion').val('{{ date('Y-m-d') }}');
                    }

                    // Mostrar el modal
                    $('#editarProductoModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener datos del producto:", error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar los datos del producto',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        };

        // Calcular automáticamente el costo en pesos cuando cambia el costo en dólares (para edición)
        $('#edit_costoDlrs').on('input', function() {
            let costoDlrs = parseFloat($(this).val()) || 0;
            let tipoCambio = parseFloat($('#edit_TC').val()) || 0;
            let costoArs = costoDlrs * tipoCambio;

            // Establecer el costo en pesos con 2 decimales
            $('#edit_costo').val(costoArs.toFixed(2));
        });

        // Configurar el TC cuando se abre el modal de edición
        $('#editarProductoModal').on('shown.bs.modal', function() {
            // Si el TC está vacío o es 0, usar el valor actual de cotización
            if (!$('#edit_TC').val() || parseFloat($('#edit_TC').val()) === 0) {
                let tipoCambio = parseFloat($('input[name="cotizacion"]').val()) || 0;
                $('#edit_TC').val(tipoCambio);

                // Recalcular el costo en pesos
                let costoDlrs = parseFloat($('#edit_costoDlrs').val()) || 0;
                if (costoDlrs > 0) {
                    let costoArs = costoDlrs * tipoCambio;
                    $('#edit_costo').val(costoArs.toFixed(2));
                }
            }
        });

        // Función para eliminar producto
        window.eliminarProducto = function(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear un formulario para enviar la solicitud DELETE
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/productos/delete/${id}`;

                    let csrfField = document.createElement('input');
                    csrfField.type = 'hidden';
                    csrfField.name = '_token';
                    csrfField.value = $('meta[name="csrf-token"]').attr('content');

                    let methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfField);
                    form.appendChild(methodField);
                    document.body.appendChild(form);

                    form.submit();
                }
            });
        };
    </script>
@endpush
