@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Numeracion de Remitos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('configuracion') }}">Configuracion General</a></li>
          <li class="breadcrumb-item active">Numeracion de Remitos</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Numeracion de Remitos</h5>
              <p class="card-text">
                Al iniciar un nuevo periodo (año), puede reiniciar la numeracion de remitos para todos los proveedores.
                Esto restablece los contadores a 1, de modo que el proximo remito de cada proveedor comenzara con la numeracion 001.
              </p>

              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Proveedor</th>
                    <th class="text-center">Contador Actual</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($camiones as $camion)
                  <tr>
                    <td>{{ $camion->proveedor->razonSocialProveedor ?? 'Sin proveedor' }}</td>
                    <td class="text-center">{{ str_pad($camion->contador, 3, '0', STR_PAD_LEFT) }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

              <form id="formReiniciar" action="{{ route('configuracion.reiniciar-numeracion') }}" method="POST">
                @csrf
                <button type="button" id="btnReiniciar" class="btn btn-danger mt-3">
                  <i class="bi bi-arrow-counterclockwise"></i> Reiniciar Numeracion
                </button>
              </form>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

@push('scripts')
<script>
  document.getElementById('btnReiniciar').addEventListener('click', function() {
    Swal.fire({
      title: 'Reiniciar Numeracion?',
      text: 'Esto restablecera todos los contadores de remitos a 1. Los remitos existentes no se veran afectados.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Si, reiniciar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('formReiniciar').submit();
      }
    });
  });
</script>
@endpush

@endsection
