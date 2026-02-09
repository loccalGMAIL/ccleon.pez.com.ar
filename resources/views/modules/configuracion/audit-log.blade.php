@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Registro de Actividad</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('configuracion') }}">Configuracion General</a></li>
          <li class="breadcrumb-item active">Registro de Actividad</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          {{-- Filtros --}}
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Filtros</h5>
              <form method="GET" action="{{ route('configuracion.audit-log') }}">
                <div class="row g-3 align-items-end">
                  <div class="col-md-2">
                    <label class="form-label">Usuario</label>
                    <select name="user_id" class="form-select">
                      <option value="">Todos</option>
                      @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                          {{ $usuario->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Modulo</label>
                    <select name="modulo" class="form-select">
                      <option value="">Todos</option>
                      @foreach($modulos as $modulo)
                        <option value="{{ $modulo }}" {{ request('modulo') == $modulo ? 'selected' : '' }}>
                          {{ ucfirst($modulo) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Accion</label>
                    <select name="accion" class="form-select">
                      <option value="">Todas</option>
                      @foreach($acciones as $accion)
                        <option value="{{ $accion }}" {{ request('accion') == $accion ? 'selected' : '' }}>
                          {{ ucfirst(str_replace('_', ' ', $accion)) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                      <i class="fa-solid fa-magnifying-glass"></i> Buscar
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          {{-- Resultados --}}
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Resultados <span class="text-muted small">({{ $logs->total() }} registros)</span></h5>

              <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Fecha/Hora</th>
                      <th>Usuario</th>
                      <th>Modulo</th>
                      <th>Accion</th>
                      <th>Descripcion</th>
                      <th class="text-center">Detalle</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($logs as $log)
                    <tr>
                      <td class="text-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                      <td>{{ $log->user_name }}</td>
                      <td>
                        <span class="badge bg-info">{{ ucfirst($log->modulo) }}</span>
                      </td>
                      <td>
                        @php
                          $badgeClass = match($log->accion) {
                            'crear' => 'bg-success',
                            'editar', 'editar_perfil' => 'bg-warning text-dark',
                            'eliminar' => 'bg-danger',
                            'cambiar_estado' => 'bg-secondary',
                            'login' => 'bg-primary',
                            'logout' => 'bg-dark',
                            default => 'bg-light text-dark',
                          };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $log->accion)) }}</span>
                      </td>
                      <td>{{ $log->descripcion }}</td>
                      <td class="text-center">
                        @if($log->datos_anteriores || $log->datos_nuevos)
                          <button class="btn btn-sm btn-outline-info btn-detalle" data-id="{{ $log->id }}" title="Ver detalle">
                            <i class="fa-solid fa-eye"></i>
                          </button>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted">No se encontraron registros</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-center">
                {{ $logs->withQueryString()->links() }}
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    {{-- Modal Detalle --}}
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalDetalleLabel">Detalle del Registro</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-3"><strong>Fecha:</strong> <span id="detFecha"></span></div>
              <div class="col-md-3"><strong>Usuario:</strong> <span id="detUsuario"></span></div>
              <div class="col-md-3"><strong>Modulo:</strong> <span id="detModulo"></span></div>
              <div class="col-md-3"><strong>Accion:</strong> <span id="detAccion"></span></div>
            </div>
            <div class="mb-3">
              <strong>Descripcion:</strong> <span id="detDescripcion"></span>
            </div>
            <div class="row">
              <div class="col-md-6">
                <h6>Datos Anteriores</h6>
                <pre id="detDatosAnteriores" class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word;"></pre>
              </div>
              <div class="col-md-6">
                <h6>Datos Nuevos</h6>
                <pre id="detDatosNuevos" class="bg-light p-3 rounded border" style="max-height: 400px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word;"></pre>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

  </main>

@push('scripts')
<script>
  $(document).on('click', '.btn-detalle', function() {
    var id = $(this).data('id');
    $.ajax({
      url: '{{ route("configuracion.audit-log.detalle", "") }}/' + id,
      type: 'GET',
      success: function(data) {
        var fecha = new Date(data.created_at);
        $('#detFecha').text(fecha.toLocaleString('es-AR'));
        $('#detUsuario').text(data.user_name);
        $('#detModulo').text(data.modulo);
        $('#detAccion').text(data.accion);
        $('#detDescripcion').text(data.descripcion);
        $('#detDatosAnteriores').text(data.datos_anteriores ? JSON.stringify(data.datos_anteriores, null, 2) : 'Sin datos');
        $('#detDatosNuevos').text(data.datos_nuevos ? JSON.stringify(data.datos_nuevos, null, 2) : 'Sin datos');
        $('#modalDetalle').modal('show');
      },
      error: function() {
        Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
      }
    });
  });
</script>
@endpush

@endsection
