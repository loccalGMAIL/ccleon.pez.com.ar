@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

<main id="main" class="main">

    <div class="pagetitle">
      <h1>Usuarios</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('usuarios') }}">Usuarios</a></li>
          <li class="breadcrumb-item active">Editar Usuario</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <form action="{{route('usuarios.update', $item->id)}}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <section class="section profile">
        <div class="row">

          <!-- Columna izquierda: Foto y datos de identidad -->
          <div class="col-xl-4">
            <div class="card">
              <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <img src="{{ $item->foto_url }}" alt="Foto de perfil" class="rounded-circle" width="120" height="120" style="object-fit: cover;" id="preview-foto">
                <h2 class="mt-3 mb-0">{{ $item->name }}</h2>
                <h3>{{ $item->perfil->nombre ?? 'Sin perfil' }}</h3>
                <span class="text-muted small">{{ $item->email }}</span>

                <div class="w-100 mt-3 px-3">
                  <label for="foto" class="form-label small text-muted">Cambiar foto de perfil</label>
                  <input type="file" class="form-control form-control-sm" name="foto" id="foto" accept="image/*" onchange="previewImage(this)">
                </div>
              </div>
            </div>
          </div>

          <!-- Columna derecha: Datos editables + Contraseña -->
          <div class="col-xl-8">

            <!-- Card: Datos del usuario -->
            <div class="card">
              <div class="card-body pt-3">
                <h5 class="card-title">Datos del usuario</h5>
                <div class="row">
                  <div class="col-lg-4 col-md-6 mb-3">
                    <label for="name" class="form-label small text-muted">Nombre de usuario</label>
                    <input type="text" class="form-control form-control-sm" name="name" id="name" required value="{{$item->name}}">
                  </div>
                  <div class="col-lg-4 col-md-6 mb-3">
                    <label for="email" class="form-label small text-muted">Email</label>
                    <input type="text" class="form-control form-control-sm" name="email" id="email" required value="{{$item->email}}">
                  </div>
                  <div class="col-lg-4 col-md-6 mb-3">
                    <label for="perfil_id" class="form-label small text-muted">Perfil</label>
                    <select name="perfil_id" id="perfil_id" class="form-select form-select-sm" required>
                      <option value="">Seleccionar perfil...</option>
                      @foreach($perfiles as $perfil)
                        <option value="{{ $perfil->id }}" {{ $item->perfil_id == $perfil->id ? 'selected' : '' }}>{{ $perfil->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card: Contraseña -->
            <div class="card">
              <div class="card-body pt-3">
                <h5 class="card-title">Contraseña</h5>
                <div class="row">
                  <div class="col-lg-6 col-md-6 mb-3">
                    <label for="password" class="form-label small text-muted">Password</label>
                    <div class="input-group input-group-sm">
                      <input type="password" class="form-control form-control-sm" name="password" id="password" required value="{{$item->password}}">
                      <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                  <a href="{{route('usuarios')}}" class="btn btn-secondary">Cancelar</a>
                  <button type="submit" class="btn btn-primary">Guardar</button>
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
