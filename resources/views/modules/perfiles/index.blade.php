@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Perfiles</h1>
            <nav>
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                  <li class="breadcrumb-item active">Perfiles</li>
                </ol>
              </nav>

        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Administrar Perfiles</h5>
                            <p class="card-text">En esta seccion podra administrar los perfiles de acceso y sus permisos por modulo.</p>
                            <a href="{{ route('perfiles.create') }}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-circle-plus"> </i> Agregar nuevo perfil
                            </a>
                            <a href="{{ route('perfiles.restricciones') }}" class="btn btn-sm btn-warning">
                                <i class="fa-solid fa-lock"></i> Restricciones de Proveedores
                            </a>
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Descripcion</th>
                                        <th class="text-center">Modulos</th>
                                        <th class="text-center">Usuarios</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                        <tr class="text-center">
                                            <td>{{ $item->nombre }}</td>
                                            <td>{{ $item->descripcion }}</td>
                                            <td><span class="badge bg-info">{{ $item->modulos_count }}</span></td>
                                            <td><span class="badge bg-secondary">{{ $item->usuarios_count }}</span></td>
                                            <td>
                                                <a href="{{ route('perfiles.edit', $item->id) }}"
                                                    class="btn btn-success btn-sm"><i
                                                        class="fa-solid fa-pen-to-square"></i></a>
                                                <a href="#" class="btn btn-danger btn-sm eliminar-perfil" data-id="{{ $item->id }}">
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
            // Manejar el evento de eliminar perfil
            $('.eliminar-perfil').on('click', function(e) {
                e.preventDefault();

                let id = $(this).data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Este perfil será eliminado permanentemente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/perfiles/destroy/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        '¡Eliminado!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'No se puede eliminar',
                                        response.message,
                                        'warning'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error',
                                    'No se pudo eliminar el perfil.',
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
