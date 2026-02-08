@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Usuarios</h1>
            <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                  <li class="breadcrumb-item active">Usuarios</li>
                </ol>
              </nav>

        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Administrar Usuarios</h5>
                            <p class="card-text">En esta sección podrá administrar las cuentas y roles de usuarios.</p>
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <a href="{{ route('usuarios.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-circle-plus"> </i> Agregar nuevo usuario
                            </a>
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Perfil</th>
                                        <th class="text-center">Activo</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="text-center">
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->perfil->nombre }}</td>
                                            <td>
                                              <div class="form-check form-switch">
                                                <input class="form-check-input cambiar-estado" type="checkbox" id="activo{{ $item->id }}" 
                                                    data-id="{{ $item->id }}" {{ $item->activo == 1 ? 'checked' : '' }}>
                                              </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('usuarios.edit', $item->id) }}"
                                                    class="btn btn-success btn-sm"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm eliminar-usuario" data-id="{{ $item->id }}">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                            </table>
                            <!-- End Table with stripped rows -->

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
            $('.cambiar-estado').on("change", function() {
                let id = $(this).data("id"); // Obtener el ID del usuario
                let estado = $(this).is(":checked") ? 1 : 0; // Determinar el nuevo estado

                // Enviar solicitud AJAX al servidor
                $.ajax({
                    url: `/usuarios/estado/${id}`, // Ruta para cambiar el estado
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // Token CSRF
                        activo: estado // Nuevo estado
                    },
                    success: function(response) {
                        // Mostrar notificación de éxito con SweetAlert
                        Swal.fire(
                            '¡Estado actualizado!',
                            response.message,
                            'success'
                        );
                    },
                    error: function(xhr) {
                        // Mostrar notificación de error con SweetAlert
                        Swal.fire(
                            'Error',
                            'No se pudo actualizar el estado del usuario.',
                            'error'
                        );
                    }
                });
            });

            // Manejar el evento de eliminar usuario
            $('.eliminar-usuario').on('click', function(e) {
                e.preventDefault(); // Evitar el comportamiento predeterminado del enlace

                let id = $(this).data('id'); // Obtener el ID del usuario

                // Mostrar confirmación con SweetAlert
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Este usuario será eliminado permanentemente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Enviar solicitud AJAX para eliminar el usuario
                        $.ajax({
                            url: `/usuarios/destroy/${id}`, // Ruta para eliminar el usuario
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}' // Token CSRF
                            },
                            success: function(response) {
                                // Mostrar notificación de éxito con SweetAlert
                                Swal.fire(
                                    '¡Eliminado!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload(); // Recargar la página para reflejar los cambios
                                });
                            },
                            error: function(xhr) {
                                // Mostrar notificación de error con SweetAlert
                                Swal.fire(
                                    'Error',
                                    'No se pudo eliminar el usuario.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
