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

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
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
