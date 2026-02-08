@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Perfiles</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('perfiles') }}">Perfiles</a></li>
          <li class="breadcrumb-item active">{{ isset($item) ? 'Editar Perfil' : 'Nuevo Perfil' }}</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">{{ isset($item) ? 'Editar perfil' : 'Agregar nuevo perfil' }}</h5>

              @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif

              <form action="{{ isset($item) ? route('perfiles.update', $item->id) : route('perfiles.store') }}" method="POST">
                  @csrf
                  @if(isset($item))
                    @method('PUT')
                  @endif

                <div class="row g-3 mb-4">
                  <div class="col-md-6">
                    <label for="nombre">Nombre del Perfil</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required
                           value="{{ old('nombre', isset($item) ? $item->nombre : '') }}">
                  </div>
                  <div class="col-md-6">
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" name="descripcion" id="descripcion"
                           value="{{ old('descripcion', isset($item) ? $item->descripcion : '') }}">
                  </div>
                </div>

                <h6 class="mb-3">Modulos de acceso</h6>
                <div class="row g-3 mb-4">
                  @foreach($modulos as $clave => $modulo)
                    <div class="col-md-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="modulos[]" value="{{ $clave }}"
                               id="modulo_{{ $clave }}"
                               {{ (isset($modulosSeleccionados) && in_array($clave, $modulosSeleccionados)) || (is_array(old('modulos')) && in_array($clave, old('modulos'))) ? 'checked' : '' }}>
                        <label class="form-check-label" for="modulo_{{ $clave }}">
                          <i class="{{ $modulo['icono'] }}"></i> {{ $modulo['nombre'] }}
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>

                <button class="btn btn-primary mt-3">Guardar</button>
                <a href="{{route('perfiles')}}" class="btn btn-info mt-3">Cancelar</a>
              </form>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

@endsection
