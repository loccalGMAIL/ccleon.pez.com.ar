@extends('layouts.main')
@section('titulo', $titulo)
@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Reclamos</h1>
            @if (isset($remito))
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('remitos') }}">Remitos</a></li>
                        <li class="breadcrumb-item active">Reclamos del Remito
                            {{ str_pad($remito->id, 6, '0', STR_PAD_LEFT) }}</li>
                    </ol>
                </nav>
            @else
            <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                  <li class="breadcrumb-item"><a href="{{ route('remitos') }}">Remitos</a></li>
                  <li class="breadcrumb-item active">Reclamos</li>
                </ol>
              </nav>
            @endif
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            @if (isset($remito))
                                <h5 class="card-title">
                                    Reclamos del Remito
                                    #{{ str_pad($remito->proveedores_id, 3, '0', STR_PAD_LEFT) }}-{{ str_pad($remito->camion, 3, '0', STR_PAD_LEFT) }}
                                </h5>
                                <p>Proveedor: {{ $remito->proveedor->razonSocialProveedor }}</p>
                                <p>Factura: {{ $remito->nroFacturaRto }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Lista de Reclamos</h5>
                                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#agregarReclamoModal">
                                        <i class="fa-solid fa-circle-plus"></i> Agregar Reclamo
                                    </a>
                                </div>
                            @else
                                <h5 class="card-title">Administrar los reclamos</h5>
                                <p class="card-text">En esta sección podrá administrar los reclamos de los envios.</p>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Table with datatable -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Fecha</th>
                                        @if (!isset($remito))
                                            <th class="text-center">Nro Remito</th>
                                        @endif
                                        <th class="text-center">Proveedor</th>
                                        <th class="text-center">Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Observaciones</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="text-center">
                                            <td>{{ $item->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                                            @if (!isset($remito))
                                                <td>{{ str_pad($item->rto->proveedores_id, 3, '0', STR_PAD_LEFT) }}-{{ str_pad($item->rto->camion, 3, '0', STR_PAD_LEFT) }}</td>
                                            @endif
                                            <td>{{ $item->rto->proveedor->razonSocialProveedor }}</td>
                                            <td>{{ $item->producto }}</td>
                                            <td>{{ $item->cantidad }}</td>
                                            <td data-tooltip="{{ $item->observaciones }}">{{ Str::limit($item->observaciones, 30) }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $item->estadoReclamoRto == 'pendiente' ? 'bg-warning' : 'bg-success' }}">
                                                    {{ ucfirst($item->estadoReclamoRto) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="badge bg-success" data-bs-toggle="modal"
                                                    data-bs-target="#editarReclamoModal{{ $item->id }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i></a>
                                                <a href="#" class="badge bg-danger eliminar-reclamo"
                                                    data-id="{{ $item->id }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- End Table with datatable -->

                            @if ($items->isEmpty())
                                <div class="alert alert-info mt-3">No hay reclamos
                                    registrados{{ isset($remito) ? ' para este remito' : '' }}.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if (isset($remito))
            <!-- Modal para agregar reclamo -->
            <div class="modal fade" id="agregarReclamoModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar Reclamo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="nuevoReclamoForm" method="POST" action="{{ route('reclamos.store') }}">
                                @csrf
                                <input type="hidden" name="Rto_id" value="{{ $remito->id }}">

                                <div class="row mb-3">
                                    <label for="producto" class="col-sm-3 col-form-label">Producto</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="producto" name="producto" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="cantidad" class="col-sm-3 col-form-label">Cantidad</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" id="cantidad" name="cantidad"
                                            step="0.01" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="observaciones" class="col-sm-3 col-form-label">Observaciones</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" required></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="estadoReclamoRto" class="col-sm-3 col-form-label">Estado</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" id="estadoReclamoRto" name="estadoReclamoRto" required
                                            onchange="toggleResolucionField('nuevo')">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="resuelto">Resuelto</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3" id="resolucionContainer" style="display: none;">
                                    <label for="resolucionReclamoRto" class="col-sm-3 col-form-label">Resolución</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="resolucionReclamoRto" name="resolucionReclamoRto" rows="3"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" form="nuevoReclamoForm" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modales para editar cada reclamo -->
        @foreach ($items as $item)
            <div class="modal fade" id="editarReclamoModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Reclamo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editarReclamoForm{{ $item->id }}" method="POST"
                                action="{{ route('reclamos.update', $item->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <label for="edit_producto_{{ $item->id }}"
                                        class="col-sm-3 col-form-label">Producto</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                            id="edit_producto_{{ $item->id }}" name="producto"
                                            value="{{ $item->producto }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="edit_cantidad_{{ $item->id }}"
                                        class="col-sm-3 col-form-label">Cantidad</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control"
                                            id="edit_cantidad_{{ $item->id }}" name="cantidad"
                                            value="{{ $item->cantidad }}" step="0.01" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="edit_observaciones_{{ $item->id }}"
                                        class="col-sm-3 col-form-label">Observaciones</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="edit_observaciones_{{ $item->id }}" name="observaciones" rows="3"
                                            required>{{ $item->observaciones }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="edit_estado_{{ $item->id }}"
                                        class="col-sm-3 col-form-label">Estado</label>
                                    <div class="col-sm-9">
                                        <select class="form-select" id="edit_estado_{{ $item->id }}"
                                            name="estadoReclamoRto"
                                            onchange="toggleResolucionField('{{ $item->id }}')" required>
                                            <option value="pendiente"
                                                {{ $item->estadoReclamoRto == 'pendiente' ? 'selected' : '' }}>Pendiente
                                            </option>
                                            <option value="resuelto"
                                                {{ $item->estadoReclamoRto == 'resuelto' ? 'selected' : '' }}>Resuelto
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3" id="resolucion_container_{{ $item->id }}"
                                    style="{{ $item->estadoReclamoRto == 'resuelto' ? 'display: block;' : 'display: none;' }}">
                                    <label for="edit_resolucion_{{ $item->id }}"
                                        class="col-sm-3 col-form-label">Resolución</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="edit_resolucion_{{ $item->id }}" name="resolucionReclamoRto" rows="3"
                                            {{ $item->estadoReclamoRto == 'resuelto' ? 'required' : '' }}>{{ $item->resolucionReclamoRto }}</textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" form="editarReclamoForm{{ $item->id }}"
                                class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


    </main>
@endsection

@push('scripts')
        <script>

            function toggleResolucionField(id) {
                const isNuevo = id === 'nuevo';
                const estadoSelect = isNuevo ?
                    document.getElementById('estadoReclamoRto') :
                    document.getElementById(`edit_estado_${id}`);
                const resolucionContainer = isNuevo ?
                    document.getElementById('resolucionContainer') :
                    document.getElementById(`resolucion_container_${id}`);
                const resolucionTextarea = isNuevo ?
                    document.getElementById('resolucionReclamoRto') :
                    document.getElementById(`edit_resolucion_${id}`);

                if (estadoSelect.value === 'resuelto') {
                    resolucionContainer.style.display = 'block';
                    resolucionTextarea.setAttribute('required', 'required');
                } else {
                    resolucionContainer.style.display = 'none';
                    resolucionTextarea.removeAttribute('required');
                }
            }



                  // Botón para eliminar un reclamo
                  const botonesEliminarReclamo = document.querySelectorAll('.eliminar-reclamo');
                  botonesEliminarReclamo.forEach(boton => {
                      boton.addEventListener('click', function () {
                          Swal.fire({
                              title: '¿Estás seguro?',
                              text: "Este reclamo será eliminado permanentemente.",
                              icon: 'warning',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Sí, eliminar',
                              cancelButtonText: 'Cancelar'
                          }).then((result) => {
                              if (result.isConfirmed) {
                                  const id = this.dataset.id;

                                  // Crear FormData para CSRF token
                                  const formData = new FormData();
                                  formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                                  formData.append('_method', 'DELETE');

                                  // Enviar solicitud para eliminar
                                  fetch(`/reclamos/destroy/${id}`, {
                                      method: 'DELETE',
                                      body: formData,
                                      headers: {
                                          'X-Requested-With': 'XMLHttpRequest'
                                      }
                                  })
                                  .then(response => response.json())
                                  .then(data => {
                                      if (data.success) {
                                          // Eliminar la fila de la tabla de manera segura
                                          const row = this.closest('tr');
                                          if (row && row.parentNode) {
                                              row.parentNode.removeChild(row);
                                          }

                                          // Mostrar notificación de éxito
                                          Swal.fire(
                                              '¡Eliminado!',
                                              'El reclamo ha sido eliminado correctamente.',
                                              'success'
                                          );
                                      } else {
                                          Swal.fire(
                                              'Error',
                                              'No se pudo eliminar el reclamo: ' + (data.message || 'Error desconocido'),
                                              'error'
                                          );
                                      }
                                  })
                                  .catch(error => {
                                      console.error('Error:', error);
                                      Swal.fire(
                                          'Error',
                                          'Ocurrió un error al procesar la solicitud.',
                                          'error'
                                      );
                                  });
                              }
                          });
                      });
            });

            // Botón para agregar un nuevo reclamo
            const btnGuardarReclamo = document.getElementById('guardarReclamo');
            if (btnGuardarReclamo) {
                btnGuardarReclamo.addEventListener('click', function() {
                    const descripcion = document.getElementById('descripcionReclamo').value.trim();

                    if (!descripcion) {
                        Swal.fire(
                            'Advertencia',
                            'Por favor, ingrese una descripción para el reclamo.',
                            'warning'
                        );
                        return;
                    }

                    // Crear FormData para enviar
                    const formData = new FormData();
                    formData.append('descripcion', descripcion);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'));

                    // Enviar solicitud para guardar
                    fetch('{{ route('reclamos.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mostrar notificación de éxito
                                Swal.fire(
                                    '¡Guardado!',
                                    'El reclamo ha sido guardado correctamente.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // Recargar la página para reflejar los cambios
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'No se pudo guardar el reclamo: ' + (data.message || 'Error desconocido'),
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al procesar la solicitud.',
                                'error'
                            );
                        });
                });
            }
            // });
            document.addEventListener('DOMContentLoaded', function () {
                // Botón para eliminar un reclamo
                const botonesEliminarReclamo = document.querySelectorAll('.eliminar-reclamo');
                botonesEliminarReclamo.forEach(boton => {
                    boton.addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Este reclamo será eliminado permanentemente.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const id = this.dataset.id;

                                // Crear FormData para CSRF token
                                const formData = new FormData();
                                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                                formData.append('_method', 'DELETE');

                                // Enviar solicitud para eliminar
                                fetch(`/reclamos/destroy/${id}`, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Eliminar la fila de la tabla de manera segura
                                        const row = boton.closest('tr');
                                        if (row && row.parentNode) {
                                            row.parentNode.removeChild(row);
                                        }

                                        // Mostrar notificación de éxito
                                        Swal.fire(
                                            '¡Eliminado!',
                                            'El reclamo ha sido eliminado correctamente.',
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error',
                                            'No se pudo eliminar el reclamo: ' + (data.message || 'Error desconocido'),
                                            'error'
                                        );
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire(
                                        'Error',
                                        'Ocurrió un error al procesar la solicitud.',
                                        'error'
                                    );
                                });
                            }
                        });
                    });
                });

                // Botón para agregar un nuevo reclamo
                const btnGuardarReclamo = document.getElementById('guardarReclamo');
                if (btnGuardarReclamo) {
                    btnGuardarReclamo.addEventListener('click', function () {
                        const descripcion = document.getElementById('descripcionReclamo').value.trim();

                        if (!descripcion) {
                            Swal.fire(
                                'Advertencia',
                                'Por favor, ingrese una descripción para el reclamo.',
                                'warning'
                            );
                            return;
                        }

                        // Crear FormData para enviar
                        const formData = new FormData();
                        formData.append('descripcion', descripcion);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        // Enviar solicitud para guardar
                        fetch('{{ route('reclamos.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Mostrar notificación de éxito
                                Swal.fire(
                                    '¡Guardado!',
                                    'El reclamo ha sido guardado correctamente.',
                                    'success'
                                ).then(() => {
                                    location.reload(); // Recargar la página para reflejar los cambios
                                });
                            } else {
                                Swal.fire(
                                    'Error',
                                    'No se pudo guardar el reclamo: ' + (data.message || 'Error desconocido'),
                                    'error'
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire(
                                'Error',
                                'Ocurrió un error al procesar la solicitud.',
                                'error'
                            );
                        });
                    });
                }
            });
        </script>

    
@endpush
