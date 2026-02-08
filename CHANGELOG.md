# Changelog

Todas las modificaciones importantes de este proyecto se documentan en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/),
y este proyecto sigue [Versionado Semantico](https://semver.org/lang/es/).

## [1.1.0] - 2025-XX-XX

### Agregado
- CRUD completo de **Productos** por proveedor (PR #24, #28)
- Cotizacion del dolar con botones de actualizacion (PR #25, #27)
- Correciones de numeracion y tamanio de botones en productos (PR #26)

## [1.0.0] - 2025-XX-XX

### Agregado
- **Dashboard** con datos reales: metricas, graficos y analisis de facturacion (PR #21)
- **Remitos (RTO)**: creacion, edicion, listado con DataTable (PR #7, #8, #9, #10)
- Detalle de remitos: carga, eliminacion y edicion de lineas (PR #8)
- Cambiar estado del remito y vista de remitos en espera (PR #16, #17)
- Actualizacion de factura y fecha en remitos (PR #18)
- **Reclamos y Observaciones**: CRUD con correccion en eliminacion (PR #20)
- **Proveedores**: vistas, controlador y CRUD completo (PR #5)
- **Camiones**: vistas y controlador (PR #6)
- **Usuarios**: logueo, CRUD, control de perfiles y roles admin/user (PR #4, #22)
- Breadcrumb en todas las vistas (PR #20)
- Activar/desactivar proveedores y usuarios (PR #19)
- Middleware de autenticacion y control de roles (`Checkrol:admin`)

### Infraestructura
- Proyecto Laravel 12 con PHP 8.2+
- Frontend con Blade + Tailwind CSS v4 + NiceAdmin theme
- Base de datos MySQL con migraciones y seeders
- Vite 6.0+ como bundler
- Migraciones iniciales y seeder de usuario admin (PR #3)
