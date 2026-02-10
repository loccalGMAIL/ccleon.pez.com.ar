<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="{{route('home')}}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

@can('acceso-remitos')

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#">
          <i class="fa-solid fa-file-signature"></i><span>Remitos</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="components-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="{{route('remitos')}}">
              <i class="bi bi-circle"></i><span>Listar</span>
            </a>
          </li>
          @can('acceso-reclamos')
          <li>
            <a href="{{route('reclamos')}}">
              <i class="bi bi-circle"></i><span>Reclamos</span>
            </a>
          </li>
          @endcan
          @can('acceso-observaciones')
          <li>
            <a href="{{route('observaciones')}}">
              <i class="bi bi-circle"></i><span>Observaciones</span>
            </a>
          </li>
          @endcan
        </ul>
      </li><!-- End Components Nav -->
      @endcan

@can('acceso-proveedores')
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('proveedores')}}">
          <i class="fa-solid fa-truck-field"></i>
          <span>Proveedores</span>
        </a>
      </li><!-- End Proveedores Nav -->
@endcan

@can('acceso-productos')
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('productos')}}">
          <i class="fa-solid fa-box-open"></i>
          <span>Productos</span>
        </a>
      </li><!-- End Productos Nav -->
@endcan

@can('acceso-informes')
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('informes')}}">
          <i class="bi bi-menu-button-wide"></i>
          <span>Informes</span>
        </a>
      </li><!-- End Informes Nav -->
@endcan

@can('acceso-logistica')
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{route('logistica')}}">
          <i class="fa-solid fa-truck-fast"></i>
          <span>Logistica</span>
        </a>
      </li><!-- End Logistica Nav -->
@endcan

@if(Auth::check() && (Auth::user()->tieneAcceso('usuarios') || Auth::user()->tieneAcceso('perfiles') || Auth::user()->tieneAcceso('configuracion')))
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#configuracion-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-gear"></i><span>Configuracion</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="configuracion-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          @can('acceso-usuarios')
          <li>
            <a href="{{route('usuarios')}}">
              <i class="bi bi-circle"></i><span>Usuarios</span>
            </a>
          </li>
          @endcan
          @can('acceso-perfiles')
          <li>
            <a href="{{route('perfiles')}}">
              <i class="bi bi-circle"></i><span>Perfiles</span>
            </a>
          </li>
          @endcan
          @can('acceso-configuracion')
          <li>
            <a href="{{route('configuracion')}}">
              <i class="bi bi-circle"></i><span>General</span>
            </a>
          </li>
          @endcan
        </ul>
      </li><!-- End Configuracion Nav -->
@endif

    </ul>

    <div class="text-center text-muted position-absolute bottom-0 start-0 end-0 pb-3" style="font-size: 0.75rem;">
      v{{ config('version.number') }}
    </div>

  </aside>
