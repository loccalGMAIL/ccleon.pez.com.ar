<div class="d-flex align-items-center">
    <label for="filtroAnio" class="form-label me-2 mb-0"><strong>Año:</strong></label>
    <select id="filtroAnio" class="form-select form-select-sm" style="width: auto;" onchange="window.location.href=this.value">
        @foreach($anios as $anio)
            <option value="{{ request()->url() }}?anio={{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>
                {{ $anio }}
            </option>
        @endforeach
    </select>
</div>
