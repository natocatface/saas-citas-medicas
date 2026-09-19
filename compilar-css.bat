@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo ==================================================================
echo   CitasMedicas - Instalacion de assets locales (sin CDN, sin npm)
echo ==================================================================
echo.

if not exist "public\js" mkdir "public\js"
if not exist "public\css" mkdir "public\css"

REM ---- 1) Tailwind CLI (binario oficial, no requiere Node/npm) ----
if not exist "tailwindcss.exe" (
    echo [1/4] Descargando Tailwind CLI...
    curl -L --fail -o tailwindcss.exe https://github.com/tailwindlabs/tailwindcss/releases/download/v3.4.16/tailwindcss-windows-x64.exe
    if errorlevel 1 ( echo  ERROR descargando Tailwind CLI. & pause & exit /b 1 )
) else ( echo [1/4] Tailwind CLI ya existe. )

REM ---- 2) Chart.js local ----
if not exist "public\js\chart.umd.min.js" (
    echo [2/4] Descargando Chart.js...
    curl -L --fail -o public\js\chart.umd.min.js https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js
    if errorlevel 1 ( echo  ERROR descargando Chart.js. & pause & exit /b 1 )
) else ( echo [2/4] Chart.js ya existe. )

REM ---- 3) Alpine.js local ----
if not exist "public\js\alpine.min.js" (
    echo [3/4] Descargando Alpine.js...
    curl -L --fail -o public\js\alpine.min.js https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js
    if errorlevel 1 ( echo  ERROR descargando Alpine.js. & pause & exit /b 1 )
) else ( echo [3/4] Alpine.js ya existe. )

REM ---- 4) Compilar CSS de Tailwind (escanea todas las vistas) ----
echo [4/4] Compilando public\css\app.css ...
tailwindcss.exe --config tailwind.cli.cjs -i resources\css\app.css -o public\css\app.css --minify
if errorlevel 1 ( echo  ERROR al compilar el CSS. & pause & exit /b 1 )

REM ---- Limpiar caches de Laravel ----
echo Limpiando cache de vistas y config...
php artisan view:clear >nul 2>&1
php artisan optimize:clear >nul 2>&1

echo.
echo  ============================================================
echo   LISTO. La app ahora carga TODO localmente (sin CDN).
echo   Abre el navegador y refresca con Ctrl + F5.
echo  ============================================================
echo.
echo  (Si cambiamos el diseno mas adelante, vuelve a ejecutar este
echo   archivo para recompilar el CSS.)
pause
