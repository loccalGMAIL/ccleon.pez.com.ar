@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Configuracion General</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Configuracion General</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">

        <div class="col-lg-4 col-md-6">
          <a href="{{ route('configuracion.numeracion') }}" class="text-decoration-none">
            <div class="card info-card">
              <div class="card-body text-center py-4">
                <i class="fa-solid fa-hashtag text-primary" style="font-size: 2.5rem;"></i>
                <h5 class="card-title mb-0">Numeracion de Remitos</h5>
                <p class="card-text text-muted small">Reiniciar contadores de numeracion por proveedor</p>
              </div>
            </div>
          </a>
        </div>

        {{-- Espacio para futuras opciones --}}
        {{-- <div class="col-lg-4 col-md-6">
          <a href="#" class="text-decoration-none">
            <div class="card info-card">
              <div class="card-body text-center py-4">
                <i class="bi bi-envelope text-success" style="font-size: 2.5rem;"></i>
                <h5 class="card-title mb-0">Notificaciones</h5>
                <p class="card-text text-muted small">Configurar alertas y notificaciones del sistema</p>
              </div>
            </div>
          </a>
        </div> --}}

      </div>
    </section>

  </main>

@endsection
