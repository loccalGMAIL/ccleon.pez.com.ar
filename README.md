# CC Leon — Sistema de Gestión Interna

Sistema de gestión interna para CC Leon (logística / cadena de suministro).

## Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Base de datos:** MySQL
- **Frontend:** Bootstrap 5 (NiceAdmin), jQuery 3.7, DataTables, SweetAlert2, Select2
- **Build:** Vite

## Instalación

```
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run build
```

## Desarrollo

```
composer dev        # Laravel + queue + Vite en paralelo
php artisan test    # Tests
./vendor/bin/pint   # Lint PHP
```

## Módulos

| Módulo | Descripción |
|--------|-------------|
| Dashboard | Panel principal con estadísticas |
| Remitos | Gestión de remitos y documentación |
| Reclamos | Reclamos sobre remitos |
| Observaciones | Observaciones sobre remitos |
| Logística | Seguimiento de pedidos y entregas |
| Proveedores | Gestión de proveedores y camiones |
| Productos | Catálogo de productos con cotización USD |
| Informes | Reportes del sistema |
| Usuarios | Administración de cuentas |
| Perfiles | Roles y permisos |
| Configuración | Configuración general y auditoría |

## Changelog

### v1.2.4
- **Productos:** URL de edición y eliminación corregida en JS (faltaba `/` antes del `id`), generaba 404 al actualizar o eliminar

### v1.2.3
- **Remitos:** botón eliminar con soft delete y permisos por perfil (`remitos_eliminar`)

### v1.2.2
- **Logística:** nueva columna "Pago" (badge Deuda/Pagado) independiente del estado logístico
- **Correcciones generales:**
  - Usuarios: contraseña no se sobreescribe si el campo viene vacío; validación en creación; hash no expuesto en HTML
  - Observaciones: variable y mensaje corregidos en destroy(); relación proveedor() rota eliminada; ruta cambiada de POST a DELETE
  - Reclamos: query innecesaria en index() eliminada; update() usa campos explícitos
  - Proveedores: validación en store() y storeCamiones(); edit() y editCamiones() usan findOrFail()
  - Productos: import Illuminate\Log corregido; actualizarCotizacionExterna() recalcula precios y registra auditoría; bloque comentado eliminado
  - Perfiles: lista de módulos en restricciones leída desde config/modulos.php
  - RtoController: mensajes de auditoría corregidos; update() captura datos anteriores
  - RtoDetalleController: audit log captura valor anterior correctamente
  - Modelos: propiedades softDelete inválidas eliminadas; relaciones proveedor() rotas eliminadas; id removido de fillable; relación camion() con FK incorrecta eliminada de rto
  - Rutas: slash faltante corregido en rutas de productos (/update/{id}, /delete/{id})
  - Seeders: DatabaseSeeder llama a PerfilSeeder y DatosIniciales; Administrador incluye logistica y configuracion

### v1.2.1
- Logística: campo rto, observaciones colapsables

### v1.2.0
- Restricciones de proveedores por perfil: whitelist + módulos donde aplica

### v1.1.0
- Sistema de auditoría (AuditLog)

### v1.0.0
- Versión inicial
