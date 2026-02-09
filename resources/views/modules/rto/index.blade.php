@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Remitos</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item active">Remitos</li>
        </ol>
      </nav>
    </div>

    <section class="section">
    <div class="row">
      <div class="col-lg-12">

      <div class="card">
        <div class="card-body">
        <h5 class="card-title">Listado de Remitos</h5>
        <p class="card-text">En esta sección podrá administrar los remitos de los envios.</p>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#agregarRemitoModal">
            <i class="fa-solid fa-circle-plus"></i> Agregar nuevo remito
          </a>
          @include('modules.rto.partials.filtroAnio')
        </div>

        <!-- Table with stripped rows -->
        <table class="table datatable">
          <thead>
          <tr>
            <th class="text-center">Nro Remito</th>
            <th class="text-center">Proveedor</th>
            <th class="text-center">Nro Factura</th>
            <th class="text-center">Observ.</th>
            <th class="text-center">Reclamos</th>
            <th class="text-center">Ingreso</th>
            <th class="text-center">Actualiación</th>
            <th class="text-center">Estado</th>
            <th class="text-center">Acciones</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($items as $item)
        <tr class="text-center">
        <td>
        {{ str_pad($item->proveedores_id, 3, '0', STR_PAD_LEFT) }}-{{ str_pad($item->camion, 3, '0', STR_PAD_LEFT) }}
        </td>
        <td>{{ $item->proveedor->razonSocialProveedor ?? 'Sin proveedor' }}</td>
        <td>{{$item->nroFacturaRto}}</td>
        <td>{{ $item->observaciones_count ?? 0 }}</td>
        <td>{{ $item->reclamos_count ?? 0 }}</td>
        <td>{{ \Carbon\Carbon::parse($item->fechaIngresoRto)->format('d/m/Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }}</td>
        <td>
        <div class="dropdown">
          <span id="badge-estado-{{ $item->id }}"
          class="badge estado-badge {{ $item->estado == 'Espera' ? 'bg-info' : ($item->estado == 'Deuda' ? 'bg-danger' : ($item->estado == 'Pagado' ? 'bg-success' : 'bg-danger')) }}"
          data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
          {{ $item->estado }}
          </span>
          <ul class="dropdown-menu" aria-labelledby="badge-estado-{{ $item->id }}">
          <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}"
          data-estado="Espera">Espera</a></li>
          <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}"
          data-estado="Deuda">Deuda</a></li>
          <li><a class="dropdown-item cambiar-estado" href="#" data-id="{{ $item->id }}"
          data-estado="Pagado">Pagado</a></li>
          </ul>
        </div>
        </td>
        <td class="d-flex justify-content-center gap-2">
        <a href="{{ route('remitos.edit', $item->id) }}" class="badge bg-success" title="Editar">
          <i class="fa-solid fa-pen-to-square"></i>
        </a>
        <a href="{{ route('observaciones.show', $item->id) }}" class="badge bg-secondary"
          title="Ver Observaciones">
          <i class="fa-solid fa-bullseye"></i>
        </a>
        <a href="{{ route('reclamos.show', $item->id) }}" class="badge bg-danger" title="Ver Reclamos">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </a>
        </td>

        </tr>
      @endforeach
        </table>
        <!-- End Table with stripped rows -->

        </div>
      </div>

      </div>
      <!-- Modal remito -->
      @include('modules.rto.modalNvoRto')
      <!-- End Table with stripped rows -->
    </div>



  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos los elementos con clase 'cambiar-estado'
    const cambiarEstadoLinks = document.querySelectorAll('.cambiar-estado');

    // Agregar event listener a cada enlace
    cambiarEstadoLinks.forEach(link => {
      link.addEventListener('click', function (e) {
      e.preventDefault();

      const id = this.dataset.id;
      const nuevoEstado = this.dataset.estado;
      const badgeElement = document.getElementById(`badge-estado-${id}`);

      // Guardar estado original en caso de error
      const estadoOriginal = badgeElement.textContent.trim();
      const claseOriginal = Array.from(badgeElement.classList).find(clase => clase.startsWith('bg-'));

      // Actualizar visualmente (optimistic UI)
      badgeElement.textContent = nuevoEstado;
      badgeElement.classList.remove('bg-info', 'bg-danger', 'bg-success');

      if (nuevoEstado === 'Espera') {
        badgeElement.classList.add('bg-info');
      } else if (nuevoEstado === 'Deuda') {
        badgeElement.classList.add('bg-danger');
      } else if (nuevoEstado === 'Pagado') {
        badgeElement.classList.add('bg-success');
      } else {
        badgeElement.classList.add('bg-danger');
      }

      // Mostrar indicador de carga (opcional)
      badgeElement.classList.add('opacity-75');

      // Crear el objeto FormData para enviar los datos
      const formData = new FormData();
      formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      formData.append('estado', nuevoEstado);
      formData.append('_method', 'POST');

      // Realizar la solicitud Fetch
      fetch(`/remitos/actualizarEstado/${id}`, {
        method: 'POST',
        body: formData,
        headers: {
        'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(response => {
        if (!response.ok) {
          throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
        })
        .then(data => {
        // Quitar indicador de carga
        badgeElement.classList.remove('opacity-75');

        })
        .catch(error => {
        console.error('Error al actualizar el estado:', error);

        // Revertir cambios visuales en caso de error
        badgeElement.textContent = estadoOriginal;
        badgeElement.classList.remove('bg-info', 'bg-danger', 'bg-success', 'opacity-75');
        if (claseOriginal) {
          badgeElement.classList.add(claseOriginal);
        }

        // Mostrar error
        if (typeof Swal !== 'undefined') {
          Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudo actualizar el estado'
          });
        } else {
          alert('No se pudo actualizar el estado');
        }
        });
      });
    });
    });
  </script>


@endsection