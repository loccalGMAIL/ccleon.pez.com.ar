<div class="modal fade" id="nuevaLogisticaModal" tabindex="-1" aria-labelledby="nuevaLogisticaModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaLogisticaModalLabel">Agregar Nuevo Registro de Logistica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nuevaLogisticaForm" method="POST" action="{{ route('logistica.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="proveedores_id" class="form-label">Proveedor *</label>
                            <select class="form-select" id="proveedores_id" name="proveedores_id" required>
                                <option value="">Seleccionar proveedor</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}">
                                        {{ $proveedor->razonSocialProveedor }}
                                        ({{ $proveedor->nombreProveedor }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_pedido" class="form-label">Fecha Pedido *</label>
                            <input type="date" class="form-control" id="fecha_pedido" name="fecha_pedido" required
                                value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="rto" class="form-label">Nro. Remito</label>
                            <input type="text" class="form-control" id="rto" name="rto" maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label for="etd" class="form-label">ETD (Salida)</label>
                            <input type="date" class="form-control" id="etd" name="etd">
                        </div>
                        <div class="col-md-6">
                            <label for="eta" class="form-label">ETA (Arribo estimado)</label>
                            <input type="date" class="form-control" id="eta" name="eta">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="destino" class="form-label">Destino</label>
                            <input type="text" class="form-control" id="destino" name="destino" maxlength="200">
                        </div>
                        <div class="col-md-6">
                            <label for="transporte" class="form-label">Transporte</label>
                            <input type="text" class="form-control" id="transporte" name="transporte" maxlength="200">
                        </div>
                    </div>

                    <div class="accordion accordion-flush" id="accordionObservaciones">
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="headingObservaciones">
                                <button class="accordion-button collapsed px-0 py-2" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseObservaciones"
                                    aria-expanded="false" aria-controls="collapseObservaciones"
                                    style="font-size: 0.875rem; background: none; box-shadow: none;">
                                    <i class="fa-solid fa-comment-dots me-2 text-muted"></i> Observaciones
                                </button>
                            </h2>
                            <div id="collapseObservaciones" class="accordion-collapse collapse"
                                aria-labelledby="headingObservaciones" data-bs-parent="#accordionObservaciones">
                                <div class="accordion-body px-0 pt-1 pb-0">
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="nuevaLogisticaForm" class="btn btn-primary">Crear</button>
            </div>
        </div>
    </div>
</div>
