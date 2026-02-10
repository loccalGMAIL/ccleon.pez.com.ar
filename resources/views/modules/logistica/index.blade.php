@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Logistica</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Logistica</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Seguimiento de Pedidos y Entregas</h5>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#nuevaLogisticaModal">
                                    <i class="fa-solid fa-circle-plus"></i> Agregar nuevo registro
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table datatable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Proveedor</th>
                                            <th class="text-center">Fecha Pedido</th>
                                            <th class="text-center">ETD</th>
                                            <th class="text-center">ETA</th>
                                            <th class="text-center">Destino</th>
                                            <th class="text-center">Transporte</th>
                                            <th class="text-center">Arribo Confirmado</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Observaciones</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr class="text-center" data-row-id="{{ $item->id }}">
                                                <td>
                                                    <div class="dropdown">
                                                        <span id="badge-proveedor-{{ $item->id }}"
                                                            class="proveedor-link"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            {{ $item->proveedor->razonSocialProveedor ?? 'Sin proveedor' }}
                                                        </span>
                                                        <ul class="dropdown-menu dropdown-menu-proveedor" aria-labelledby="badge-proveedor-{{ $item->id }}">
                                                            @foreach ($proveedores as $proveedor)
                                                                <li><a class="dropdown-item cambiar-proveedor" href="#"
                                                                    data-id="{{ $item->id }}"
                                                                    data-proveedor-id="{{ $proveedor->id }}"
                                                                    data-proveedor-nombre="{{ $proveedor->razonSocialProveedor }}">
                                                                    {{ $proveedor->razonSocialProveedor }} ({{ $proveedor->nombreProveedor }})
                                                                </a></li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}"
                                                    data-field="fecha_pedido" data-type="date"
                                                    data-raw-value="{{ $item->fecha_pedido?->format('Y-m-d') }}">
                                                    {{ $item->fecha_pedido?->format('d/m/Y') }}
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}" data-field="etd"
                                                    data-type="date"
                                                    data-raw-value="{{ $item->etd?->format('Y-m-d') }}">
                                                    {{ $item->etd?->format('d/m/Y') }}
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}" data-field="eta"
                                                    data-type="date"
                                                    data-raw-value="{{ $item->eta?->format('Y-m-d') }}">
                                                    {{ $item->eta?->format('d/m/Y') }}
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}"
                                                    data-field="destino" data-type="text">
                                                    {{ $item->destino }}
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}"
                                                    data-field="transporte" data-type="text">
                                                    {{ $item->transporte }}
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}"
                                                    data-field="arribo_confirmado" data-type="date"
                                                    data-raw-value="{{ $item->arribo_confirmado?->format('Y-m-d') }}">
                                                    {{ $item->arribo_confirmado?->format('d/m/Y') }}
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeClass = match($item->estado) {
                                                            'Pendiente' => 'bg-warning',
                                                            'En transito' => 'bg-info',
                                                            'Arribado' => 'bg-success',
                                                            'Demorado' => 'bg-danger',
                                                            'Cerrado' => 'bg-secondary',
                                                            default => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <div class="dropdown">
                                                        <span id="badge-estado-{{ $item->id }}"
                                                            class="badge estado-badge {{ $badgeClass }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false"
                                                            style="cursor: pointer;">
                                                            {{ $item->estado }}
                                                        </span>
                                                        <ul class="dropdown-menu" aria-labelledby="badge-estado-{{ $item->id }}">
                                                            <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}" data-estado="Pendiente">Pendiente</a></li>
                                                            <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}" data-estado="En transito">En transito</a></li>
                                                            <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}" data-estado="Arribado">Arribado</a></li>
                                                            <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}" data-estado="Demorado">Demorado</a></li>
                                                            <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}" data-estado="Cerrado">Cerrado</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td class="editable-cell" data-id="{{ $item->id }}"
                                                    data-field="observaciones" data-type="text">
                                                    {{ Str::limit($item->observaciones, 30) }}
                                                </td>
                                                <td>
                                                    <a href="#" class="badge bg-danger eliminar-registro"
                                                        data-id="{{ $item->id }}">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            @include('modules.logistica.modalNuevaLogistica')

        </section>

    </main>

    <style>
        .editable-cell {
            cursor: pointer;
            position: relative;
        }

        .editable-cell:hover {
            background-color: #f5f5f5;
        }

        .editable-cell:hover::after {
            content: '\270E';
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 12px;
        }

        .editable-cell.editing {
            padding: 0 !important;
            background-color: #e8f4ff !important;
        }

        .editable-cell.editing::after {
            content: none;
        }

        .editable-cell input,
        .editable-cell select {
            width: 100%;
            height: 100%;
            border: 2px solid #0d6efd;
            padding: 0.375rem 0.5rem;
            outline: none;
            font-size: 0.875rem;
        }

        .celda-desactivada {
            background-color: #f0f0f0;
            color: #999;
            cursor: not-allowed;
        }

        .dropdown-menu-proveedor {
            max-height: 250px;
            overflow-y: auto;
        }

        .proveedor-link {
            cursor: pointer;
            border-bottom: 1px dashed #6c757d;
        }

        .proveedor-link:hover {
            color: #0d6efd;
        }
    </style>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Flash messages via SweetAlert
            @if (session('swal_success'))
                Swal.fire({
                    title: '',
                    text: "{{ session('swal_success') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            @if (session('swal_error'))
                Swal.fire({
                    title: 'Error',
                    text: "{{ session('swal_error') }}",
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif

            // --- Datos para dropdowns ---
            const estadoBadgeClass = {
                'Pendiente': 'bg-warning',
                'En transito': 'bg-info',
                'Arribado': 'bg-success',
                'Demorado': 'bg-danger',
                'Cerrado': 'bg-secondary',
            };

            // --- Edicion inline ---
            let activeEditCell = null;

            function startEditing(cell) {
                if (cell.classList.contains('celda-desactivada')) return;

                // Si ya hay una celda en edicion, guardarla primero
                if (activeEditCell && activeEditCell !== cell) {
                    finishEditing(true);
                }

                activeEditCell = cell;
                const type = cell.dataset.type;
                const rawValue = cell.dataset.rawValue || cell.textContent.trim();
                const field = cell.dataset.field;

                // Guardar texto original para cancelar
                cell.setAttribute('data-original-text', cell.innerHTML);

                let input;

                if (type === 'date') {
                    input = document.createElement('input');
                    input.type = 'date';
                    input.value = rawValue || '';
                } else {
                    // text
                    input = document.createElement('input');
                    input.type = 'text';
                    input.value = rawValue || '';
                }

                // Limpiar la celda
                while (cell.firstChild) {
                    cell.removeChild(cell.firstChild);
                }

                cell.classList.add('editing');
                cell.appendChild(input);
                input.focus();

                // Eventos
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        finishEditing(true);
                    } else if (e.key === 'Escape') {
                        e.preventDefault();
                        finishEditing(false);
                    }
                });

                input.addEventListener('blur', function() {
                    // Timeout para permitir que un click en otra celda cancele el blur
                    setTimeout(function() {
                        if (activeEditCell === cell) {
                            finishEditing(true);
                        }
                    }, 150);
                });
            }

            function finishEditing(save) {
                if (!activeEditCell) return;

                const cell = activeEditCell;
                const input = cell.querySelector('input, select');

                if (!input) {
                    restoreCell(cell);
                    return;
                }

                if (save) {
                    saveCell(cell, input.value, input.tagName === 'SELECT' ? input.options[input.selectedIndex]?.text : null);
                } else {
                    const originalHTML = cell.getAttribute('data-original-text') || '';
                    cell.innerHTML = originalHTML;
                    cell.classList.remove('editing');
                    activeEditCell = null;
                }
            }

            function restoreCell(cell, html) {
                if (!cell) return;
                cell.classList.remove('editing');
                if (html !== undefined) {
                    cell.innerHTML = html;
                }
                if (cell === activeEditCell) {
                    activeEditCell = null;
                }
            }

            function saveCell(cell, value, displayText) {
                if (!cell) return;

                const id = cell.dataset.id;
                const field = cell.dataset.field;
                const type = cell.dataset.type;

                if (!id || !field) {
                    restoreCell(cell, cell.getAttribute('data-original-text') || '');
                    return;
                }

                // Mostrar valor temporal mientras se guarda
                cell.classList.remove('editing');
                cell.textContent = 'Guardando...';

                const formData = new FormData();
                formData.append('id', id);
                formData.append('field', field);
                formData.append('value', value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                fetch('{{ route("logistica.actualizarCampo") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error HTTP: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Actualizar celda con el valor guardado
                        if (type === 'date') {
                            if (value) {
                                const parts = value.split('-');
                                cell.textContent = parts[2] + '/' + parts[1] + '/' + parts[0];
                            } else {
                                cell.textContent = '';
                            }
                            cell.dataset.rawValue = value || '';
                        } else {
                            cell.textContent = value;
                        }
                        activeEditCell = null;
                    } else {
                        // Restaurar valor original en caso de error
                        cell.innerHTML = cell.getAttribute('data-original-text') || '';
                        activeEditCell = null;
                        Swal.fire('Error', data.message || 'No se pudo guardar el campo', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    cell.innerHTML = cell.getAttribute('data-original-text') || '';
                    activeEditCell = null;
                    Swal.fire('Error', 'Error en la comunicacion con el servidor', 'error');
                });
            }

            // Inicializar celdas editables
            document.querySelectorAll('.editable-cell').forEach(function(cell) {
                cell.addEventListener('click', function() {
                    startEditing(this);
                });
            });

            // --- Cambiar proveedor (dropdown badge) ---
            document.querySelectorAll('.cambiar-proveedor').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const id = this.dataset.id;
                    const nuevoProveedorId = this.dataset.proveedorId;
                    const nuevoProveedorNombre = this.dataset.proveedorNombre;
                    const badgeElement = document.getElementById('badge-proveedor-' + id);

                    // Guardar valor original en caso de error
                    const nombreOriginal = badgeElement.textContent.trim();

                    // Optimistic UI update
                    badgeElement.textContent = nuevoProveedorNombre;
                    badgeElement.classList.add('opacity-75');

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('field', 'proveedores_id');
                    formData.append('value', nuevoProveedorId);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    fetch('{{ route("logistica.actualizarCampo") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) throw new Error('Error HTTP: ' + response.status);
                        return response.json();
                    })
                    .then(function(data) {
                        badgeElement.classList.remove('opacity-75');
                        if (data.success) {
                            badgeElement.textContent = data.displayValue || nuevoProveedorNombre;
                        } else {
                            badgeElement.textContent = nombreOriginal;
                            Swal.fire('Error', data.message || 'No se pudo actualizar el proveedor', 'error');
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        badgeElement.textContent = nombreOriginal;
                        badgeElement.classList.remove('opacity-75');
                        Swal.fire('Error', 'No se pudo actualizar el proveedor', 'error');
                    });
                });
            });

            // --- Cambiar estado (dropdown badge) ---
            document.querySelectorAll('.cambiar-estado').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const id = this.dataset.id;
                    const nuevoEstado = this.dataset.estado;
                    const badgeElement = document.getElementById('badge-estado-' + id);

                    // Guardar estado original en caso de error
                    const estadoOriginal = badgeElement.textContent.trim();
                    const claseOriginal = Array.from(badgeElement.classList).find(function(c) { return c.startsWith('bg-'); });

                    // Optimistic UI update
                    badgeElement.textContent = nuevoEstado;
                    badgeElement.classList.remove('bg-warning', 'bg-info', 'bg-success', 'bg-danger', 'bg-secondary');
                    badgeElement.classList.add(estadoBadgeClass[nuevoEstado] || 'bg-secondary');
                    badgeElement.classList.add('opacity-75');

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('field', 'estado');
                    formData.append('value', nuevoEstado);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    fetch('{{ route("logistica.actualizarCampo") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) throw new Error('Error HTTP: ' + response.status);
                        return response.json();
                    })
                    .then(function(data) {
                        badgeElement.classList.remove('opacity-75');
                        if (!data.success) {
                            // Revertir
                            badgeElement.textContent = estadoOriginal;
                            badgeElement.classList.remove('bg-warning', 'bg-info', 'bg-success', 'bg-danger', 'bg-secondary');
                            if (claseOriginal) badgeElement.classList.add(claseOriginal);
                            Swal.fire('Error', data.message || 'No se pudo actualizar el estado', 'error');
                        }
                    })
                    .catch(function(error) {
                        console.error('Error:', error);
                        badgeElement.textContent = estadoOriginal;
                        badgeElement.classList.remove('bg-warning', 'bg-info', 'bg-success', 'bg-danger', 'bg-secondary', 'opacity-75');
                        if (claseOriginal) badgeElement.classList.add(claseOriginal);
                        Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                    });
                });
            });

            // --- Eliminar registro ---
            document.querySelectorAll('.eliminar-registro').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const row = this.closest('tr');

                    Swal.fire({
                        title: 'Estas seguro?',
                        text: 'Este registro sera eliminado.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            fetch('/logistica/delete/' + id, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(function(response) {
                                if (!response.ok) throw new Error('Error HTTP: ' + response.status);
                                return response.json();
                            })
                            .then(function(data) {
                                if (data.success) {
                                    if (row && row.parentNode) {
                                        row.parentNode.removeChild(row);
                                    }
                                    Swal.fire('Eliminado!', 'El registro ha sido eliminado.', 'success');
                                } else {
                                    Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                                }
                            })
                            .catch(function(error) {
                                console.error('Error:', error);
                                Swal.fire('Error', 'Ocurrio un error al procesar la solicitud.', 'error');
                            });
                        }
                    });
                });
            });

        });
    </script>
@endpush

@endsection
