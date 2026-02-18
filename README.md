# CC Leon - Sistema de Gestion Interna

Sistema interno de gestion para CC Leon (logistica y cadena de suministro).

## Tech Stack

- **Backend:** Laravel 12 / PHP 8.2+ / MySQL
- **Frontend:** Bootstrap 5 (NiceAdmin) / jQuery / DataTables / SweetAlert2

## Modulos

| Modulo | Descripcion |
|---|---|
| Dashboard | Panel principal con estadisticas |
| Remitos | Gestion de remitos y documentacion |
| Reclamos | Reclamos asociados a remitos |
| Observaciones | Observaciones sobre remitos |
| Proveedores | Proveedores y camiones |
| Productos | Catalogo de productos y cotizaciones (USD) |
| Logistica | Seguimiento de pedidos y entregas |
| Informes | Reportes del sistema |
| Usuarios | Administracion de cuentas |
| Perfiles | Roles, permisos y restricciones por proveedor |
| Configuracion | Configuracion general y registro de actividad |

## Instalacion

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Desarrollo

```bash
# Servidor de desarrollo (Laravel + queue + Vite)
composer dev

# Tests
php artisan test

# Lint
./vendor/bin/pint
```

## Autorizacion

Sistema de dos capas: **Perfiles** (roles con modulos asignados) + middleware `Checkrol`. Los perfiles pueden restringir proveedores visibles por modulo. Los modulos se registran en `config/modulos.php` y los gates se generan automaticamente.
