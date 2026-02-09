@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Mi Perfil</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Mi Perfil</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form action="{{ route('mi-perfil.actualizar') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <section class="section profile">
        <div class="row">

          <!-- Columna izquierda: Foto y datos de identidad -->
          <div class="col-xl-4">
            <div class="card">
              <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <img src="{{ $user->foto_url }}" alt="Foto de perfil" class="rounded-circle" width="120" height="120" style="object-fit: cover;" id="preview-foto">
                <h2 class="mt-3 mb-0">{{ $user->name }}</h2>
                <h3>{{ $user->perfil->nombre }}</h3>
                <span class="text-muted small">{{ $user->email }}</span>

                <div class="w-100 mt-3 px-3">
                  <label for="foto" class="form-label small text-muted">Cambiar foto de perfil</label>
                  <input type="file" class="form-control form-control-sm" name="foto" id="foto" accept="image/*" onchange="previewImage(this)">
                </div>
              </div>
            </div>
          </div>

          <!-- Columna derecha: Info + Contraseña -->
          <div class="col-xl-8">

            <!-- Card: Información de la cuenta -->
            <div class="card">
              <div class="card-body pt-3">
                <h5 class="card-title">Información de la cuenta</h5>
                <div class="row">
                  <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label small text-muted">Nombre</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $user->name }}" readonly>
                  </div>
                  <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label small text-muted">Email</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $user->email }}" readonly>
                  </div>
                  <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label small text-muted">Perfil</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $user->perfil->nombre }}" readonly>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card: Cambiar contraseña -->
            <div class="card">
              <div class="card-body pt-3">
                <h5 class="card-title">Cambiar contraseña</h5>
                <div class="row">
                  <div class="col-lg-6 col-md-6 mb-3">
                    <label for="password" class="form-label small text-muted">Nueva contraseña</label>
                    <div class="input-group input-group-sm">
                      <input type="password" class="form-control form-control-sm" name="password" id="password" placeholder="Dejar en blanco para no cambiar">
                      <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label small text-muted">Confirmar contraseña</label>
                    <div class="input-group input-group-sm">
                      <input type="password" class="form-control form-control-sm" name="password_confirmation" id="password_confirmation" placeholder="Confirmar nueva contraseña">
                      <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', this)">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="text-end">
                  <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
              </div>
            </div>

          </div>

        </div>
      </section>

    </form>

  </main>

<script>
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('preview-foto').src = e.target.result;
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
  }
</script>

@endsection
