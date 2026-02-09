@extends('layouts.main')

@section('titulo', $titulo)

@section('contenido')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <!-- Botones de acción rápida -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Acciones Rápidas</h5>
                            <div class="d-flex flex-wrap gap-4">
                                @can('acceso-remitos')
                                <!-- Botón Nuevo Remito -->
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#agregarRemitoModal">
                                    <i class="fa-solid fa-circle-plus"></i> Agregar nuevo remito
                                </a>

                                <!-- Botón Ver Remitos Pendientes -->
                                <a href="{{ route('remitos.pendientes') }}" class="btn btn-warning">
                                    <i class="fa-solid fa-clipboard-list"></i> Remitos Pendientes
                                </a>
                                @endcan

                                @can('acceso-reclamos')
                                <!-- Botón Ver Reclamos -->
                                <a href="{{ route('reclamos') }}" class="btn btn-danger">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Ver Reclamos
                                </a>
                                @endcan

                                @can('acceso-observaciones')
                                <!-- Botón Ver Observaciones -->
                                <a href="{{ route('observaciones') }}" class="btn btn-success">
                                    <i class="fa-solid fa-eye"></i> Ver Observaciones
                                </a>
                                @endcan

                                <!-- Filtro por Año -->
                                <div class="d-flex align-items-center ms-auto">
                                    <label for="filtroAnioDash" class="form-label me-2 mb-0"><strong>Año:</strong></label>
                                    <select id="filtroAnioDash" class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
                                        <option value="{{ route('home') }}?anio=todos" {{ $anioSeleccionado == 'todos' ? 'selected' : '' }}>Todos</option>
                                        @foreach($anios as $anio)
                                            <option value="{{ route('home') }}?anio={{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @can('acceso-remitos')
            <!-- Tarjetas informativas -->
            <div class="row">
                <!-- Tarjeta Total Remitos -->
                <div class="col-md-6 col-lg-3">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Remitos</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $totalRemitos ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta Remitos en Espera -->
                <div class="col-md-6 col-lg-3">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Remitos en Espera</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $remitosEnEspera ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta Remitos con Deuda -->
                <div class="col-md-6 col-lg-3">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Remitos con Deuda</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $remitosConDeuda ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta Remitos Pagados -->
                <div class="col-md-6 col-lg-3">
                    <div class="card info-card customers-card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filtrar</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Hoy</a></li>
                                <li><a class="dropdown-item" href="#">Este Mes</a></li>
                                <li><a class="dropdown-item" href="#">Este Año</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Remitos Pagados</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-check-circle"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $remitosPagados ?? 0 }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <!-- Gráfico: Remitos por Mes -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Remitos por Mes</h5>
                            <div id="remitosPorMesChart" style="min-height: 365px;">
                                <canvas id="remitosMensualChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico: Top Proveedores -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Facturacion por Mes</h5>
                            <div id="topProveedoresChart" style="min-height: 365px;">
                                <canvas id="proveedoresChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remitos Recientes -->
            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">
                        <div class="card-body">
                            <h5 class="card-title">Remitos Recientes</h5>
                            <table class="table table-borderless" id="tablaRemitos">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Proveedor</th>
                                        <th scope="col">Factura</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($remitosRecientes ?? [] as $remito)
                                        <tr>
                                            <th scope="row"><a href="#">{{ $remito->id }}</a></th>
                                            <td>{{ $remito->proveedor->nombreProveedor }}</td>
                                            <td>{{ $remito->nroFacturaRto }}</td>
                                            <td>{{ $remito->fechaIngresoRto }}</td>
                                            <td>${{ number_format($remito->totalFinalRto, 2) }}</td>
                                            <td>
                                                @if ($remito->estado == 'Pagado')
                                                    <span class="badge bg-warning">Pagado</span>
                                                @elseif($remito->estado == 'Espera')
                                                    <span class="badge bg-success">Espera</span>
                                                @elseif($remito->estado == 'Deuda')
                                                    <span class="badge bg-danger">Deuda</span>
                                                @else
                                                    <span class="badge bg-secondary">Desconocido</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay remitos recientes</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal remito -->
            @include('modules.rto.modalNvoRto')
            <!-- End Table with stripped rows -->
            @endcan
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Verificar que Chart.js esté cargado
            if (typeof Chart === 'undefined') {
                console.error(
                    'Chart.js no está cargado. Por favor verifica que está incluido en el layout principal.');
                return;
            }

            // Gráfico de remitos por mes
            const remitosMensualCtx = document.getElementById('remitosMensualChart');
            if (remitosMensualCtx) {
                // Datos desde el controlador
                const mesesLabels = @json($mesesLabels ?? []);
                const mesesData = @json($mesesData ?? []);

                new Chart(remitosMensualCtx, {
                    type: 'line',
                    data: {
                        labels: mesesLabels.length > 0 ? mesesLabels : ['Ene', 'Feb', 'Mar', 'Abr', 'May',
                            'Jun'
                        ],
                        datasets: [{
                            label: 'Remitos',
                            data: mesesData.length > 0 ? mesesData : [0, 0, 0, 0, 0, 0],
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Gráfico de facturación por proveedor
            const proveedoresCtx = document.getElementById('proveedoresChart');
            if (proveedoresCtx) {
                // Datos desde el controlador
                const proveedoresLabels = @json($proveedoresLabels ?? []);
                const proveedoresData = @json($proveedoresData ?? []);

                new Chart(proveedoresCtx, {
                    type: 'bar',
                    data: {
                        labels: proveedoresLabels.length > 0 ? proveedoresLabels : ['Sin datos'],
                        datasets: [{
                            label: 'Facturación Total ($)',
                            data: proveedoresData.length > 0 ? proveedoresData : [0],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)',
                                'rgba(54, 162, 235, 0.2)',
                                'rgba(255, 206, 86, 0.2)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // Formateo de números para mostrar como moneda
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += '$' + context.parsed.y.toLocaleString();
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        $(document).ready(function() {
            // Inicializar DataTable para la tabla de remitos recientes
            $('#tablaRemitos').DataTable({
                paging: false,
                searching: false,
                info: false, // Deshabilitar información de la tabla (ej. "Mostrando 1 de 5")
                ordering: true, // Mantener ordenamiento
                columnDefs: [{
                    targets: 'no-sort', // Clase para columnas sin ordenamiento
                    orderable: false
                }],
                language: {
                    emptyTable: "No hay remitos recientes para mostrar"
                }
            });
        });
    </script>
@endpush
