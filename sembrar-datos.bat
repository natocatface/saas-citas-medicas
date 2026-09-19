@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo ============================================================
echo   CitasMedicas - Sembrar datos DEMO para el Dashboard
echo ============================================================
echo.
echo Insertando 10 registros por modulo y citas distribuidas
echo en los ultimos 7 dias y 6 meses...
echo.
php artisan db:seed --class=DemoDashboardSeeder --force
echo.
echo Limpiando cache de vistas...
php artisan view:clear
echo.
echo ============================================================
echo   LISTO. Recarga el dashboard (Ctrl+F5) para ver los datos.
echo ============================================================
pause
