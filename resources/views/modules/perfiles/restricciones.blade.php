@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Restricciones de Proveedores</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('perfiles') }}">Perfiles</a></li>
                    <li class="breadcrumb-item active">Restricciones</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Configurar restricciones por perfil</h5>
                            <p class="card-text">Seleccione un perfil para configurar que proveedores puede ver y en que modulos aplica la restriccion. Si no se activan restricciones, el perfil vera todos los proveedores.</p>

                            {{-- Selector de perfil --}}
                            <div class="mb-4">
                                <label for="selectPerfil" class="form-label fw-bold">Perfil</label>
                                <select id="selectPerfil" class="form-select">
                                    <option value="">-- Seleccione un perfil --</option>
                                    @foreach ($perfiles as $perfil)
                                        <option value="{{ $perfil->id }}">{{ $perfil->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Contenido de restricciones (oculto hasta seleccionar perfil) --}}
                            <div id="restriccionesContainer" style="display: none;">

                                {{-- Toggle de restriccion --}}
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleRestriccion">
                                        <label class="form-check-label fw-bold" for="toggleRestriccion">
                                            Restringir proveedores para este perfil
                                        </label>
                                    </div>
                                    <small class="text-muted">Si esta desactivado, el perfil vera todos los proveedores en todos los modulos.</small>
                                </div>

                                {{-- Panel de configuracion --}}
                                <div id="panelRestriccion" style="display: none;">

                                    {{-- Modulos donde aplica --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Modulos donde aplica la restriccion</label>
                                        <small class="d-block text-muted mb-2">Solo en los modulos seleccionados se filtraran los proveedores. En los demas, el perfil vera todo.</small>
                                        <div class="row">
                                            @foreach ($modulosDisponibles as $key => $nombre)
                                                <div class="col-md-3 col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input modulo-check" type="checkbox" value="{{ $key }}" id="modulo_{{ $key }}">
                                                        <label class="form-check-label" for="modulo_{{ $key }}">{{ $nombre }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Proveedores permitidos --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Proveedores permitidos</label>
                                        <small class="d-block text-muted mb-2">El perfil solo vera datos de los proveedores seleccionados en los modulos restringidos.</small>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnSeleccionarTodos">Seleccionar todos</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeseleccionarTodos">Deseleccionar todos</button>
                                        </div>
                                        <div class="row" id="listaProveedores">
                                            @foreach ($proveedores as $prov)
                                                <div class="col-md-4 col-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input proveedor-check" type="checkbox" value="{{ $prov->id }}" id="prov_{{ $prov->id }}">
                                                        <label class="form-check-label" for="prov_{{ $prov->id }}">{{ $prov->razonSocialProveedor }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Boton guardar --}}
                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" id="btnGuardar">
                                        <i class="fa-solid fa-floppy-disk"></i> Guardar restricciones
                                    </button>
                                    <a href="{{ route('perfiles') }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-arrow-left"></i> Volver a Perfiles
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                {{-- Panel de ayuda --}}
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fa-solid fa-circle-info"></i> Como funciona</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><strong>Sin restriccion:</strong> El perfil ve todos los proveedores en todos los modulos (comportamiento por defecto).</li>
                                <li class="mb-2"><strong>Con restriccion:</strong> El perfil solo ve datos de los proveedores seleccionados, pero unicamente en los modulos marcados.</li>
                                <li class="mb-2"><strong>Ejemplo:</strong> Si selecciona "Proveedor X" y marca solo "Logistica", el perfil vera solo datos de Proveedor X en Logistica, pero vera todo en Remitos, Productos, etc.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const $selectPerfil = $('#selectPerfil');
            const $container = $('#restriccionesContainer');
            const $toggle = $('#toggleRestriccion');
            const $panel = $('#panelRestriccion');

            // Al cambiar perfil, cargar restricciones actuales
            $selectPerfil.on('change', function() {
                const perfilId = $(this).val();
                if (!perfilId) {
                    $container.hide();
                    return;
                }

                // Reset
                $toggle.prop('checked', false);
                $panel.hide();
                $('.proveedor-check').prop('checked', false);
                $('.modulo-check').prop('checked', false);

                // Cargar datos
                $.ajax({
                    url: `/perfiles/restricciones/${perfilId}`,
                    method: 'GET',
                    success: function(data) {
                        if (data.success) {
                            const tieneRestriccion = data.proveedores.length > 0;
                            $toggle.prop('checked', tieneRestriccion);
                            $panel.toggle(tieneRestriccion);

                            // Marcar proveedores
                            data.proveedores.forEach(function(id) {
                                $(`#prov_${id}`).prop('checked', true);
                            });

                            // Marcar modulos
                            data.modulos.forEach(function(mod) {
                                $(`#modulo_${mod}`).prop('checked', true);
                            });
                        }
                        $container.show();
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudieron cargar las restricciones', 'error');
                    }
                });
            });

            // Toggle mostrar/ocultar panel
            $toggle.on('change', function() {
                $panel.toggle($(this).is(':checked'));
                if (!$(this).is(':checked')) {
                    $('.proveedor-check').prop('checked', false);
                    $('.modulo-check').prop('checked', false);
                }
            });

            // Seleccionar/deseleccionar todos
            $('#btnSeleccionarTodos').on('click', function() {
                $('.proveedor-check').prop('checked', true);
            });
            $('#btnDeseleccionarTodos').on('click', function() {
                $('.proveedor-check').prop('checked', false);
            });

            // Guardar
            $('#btnGuardar').on('click', function() {
                const perfilId = $selectPerfil.val();
                if (!perfilId) {
                    Swal.fire('Atencion', 'Seleccione un perfil primero', 'warning');
                    return;
                }

                let proveedores = [];
                let modulos = [];

                if ($toggle.is(':checked')) {
                    $('.proveedor-check:checked').each(function() {
                        proveedores.push($(this).val());
                    });
                    $('.modulo-check:checked').each(function() {
                        modulos.push($(this).val());
                    });

                    if (proveedores.length === 0) {
                        Swal.fire('Atencion', 'Debe seleccionar al menos un proveedor o desactivar la restriccion', 'warning');
                        return;
                    }
                    if (modulos.length === 0) {
                        Swal.fire('Atencion', 'Debe seleccionar al menos un modulo donde aplicar la restriccion', 'warning');
                        return;
                    }
                }

                $.ajax({
                    url: '{{ route("perfiles.guardarRestriccion") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        perfil_id: perfilId,
                        proveedores: proveedores,
                        modulos: modulos
                    },
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Guardado', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message || 'Error al guardar', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Error al guardar las restricciones';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });
    </script>
@endpush
