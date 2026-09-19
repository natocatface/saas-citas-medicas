# CitasMédicas — SaaS de Citas Médicas (Laravel 11 + MySQL + Tailwind)

Sistema web para la gestión de citas médicas. Esta primera entrega incluye la **base del
proyecto**, **autenticación (login)**, **layout con menú vertical de 17 módulos** y un
**Dashboard funcional** con métricas y gráficos reales tomados de la base de datos.

## Stack

- **Laravel 11** (PHP 8.2+)
- **MySQL** (`saas_citasmedicas`)
- **Blade + Tailwind CSS** (vía CDN, sin paso de compilación)
- **Chart.js** para los gráficos del dashboard

## Requisitos previos

- PHP **8.2 o superior** con extensiones `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- **Composer**
- **MySQL** corriendo en `localhost:3306`

## Instalación (Windows)

Desde la carpeta del proyecto (`C:\SAAS\saas_citasmedicas`):

```bash
# 1) Instalar dependencias de Laravel
composer install

# 2) Configurar el entorno
copy .env.example .env
php artisan key:generate

# 3) Crear la base de datos en MySQL (una sola vez)
#    Opción A — por consola MySQL:
#    CREATE DATABASE saas_citasmedicas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#
#    Opción B — comando rápido (ajusta el usuario si tienes contraseña):
mysql -u root -e "CREATE DATABASE IF NOT EXISTS saas_citasmedicas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4) Migrar y cargar datos de ejemplo (médicos, pacientes y ~391 citas)
php artisan migrate --seed

# 5) Levantar el servidor
php artisan serve
```

Luego abre: **http://127.0.0.1:8000**

> Si tu MySQL tiene contraseña para `root`, edítala en `.env` (`DB_PASSWORD=...`).

## Credenciales de acceso (demo)

| Rol        | Correo                        | Contraseña |
|------------|-------------------------------|------------|
| Admin      | `admin@citasmedicas.test`     | `password` |
| Recepción  | `recepcion@citasmedicas.test` | `password` |

## Módulos del sistema

El menú vertical ya incluye los 17 módulos. **Dashboard** está completamente funcional;
el resto están enlazados y muestran una pantalla "en construcción" lista para desarrollar
su CRUD en próximas iteraciones:

Dashboard · Citas · Lista de Espera · Calendario · Pacientes · Médicos · Recetas Médicas ·
Plantillas (CIE-10) · Mensajes · Facturación · Aseguradores · Reportes · Especialidades ·
Usuarios · Mantenimiento · Plantillas de Email · Configuración.

## Estructura relevante

```
app/
  Http/Controllers/      LoginController, DashboardController, ModuleController
  Models/                User, Especialidad, Medico, Paciente, Aseguradora, Cita
config/modulos.php       Definición central del menú lateral
database/
  migrations/            Tablas del dominio (especialidades, médicos, pacientes, citas...)
  seeders/               Datos de ejemplo que alimentan el dashboard
resources/views/
  layouts/app.blade.php  Sidebar + topbar
  auth/login.blade.php   Pantalla de login
  dashboard.blade.php    Dashboard con tarjetas y gráficos
  modules/placeholder    Pantalla genérica de módulo
  partials/icon          Iconos SVG (Heroicons)
routes/web.php           Rutas (login, dashboard, módulos)
```

## Notas de producción

- Para producción conviene migrar Tailwind del CDN a un build con **Vite** (`npm install`,
  `@vite` en los layouts) para mejor rendimiento y purga de clases.
- Cambia `APP_ENV=production`, `APP_DEBUG=false` y credenciales reales antes de desplegar.
