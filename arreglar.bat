@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo ============================================================
echo   CitasMedicas - Descongelar vistas (limpiar cache Laravel)
echo ============================================================
echo.
echo Limpiando TODAS las caches (vistas compiladas, rutas, config)...
php artisan optimize:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan cache:clear
echo.
echo  ============================================================
echo   LISTO. Ahora cierra y abre el navegador, o pulsa Ctrl+F5.
echo  ============================================================
pause
