@echo off
echo Configurando permisos para Laravel...

REM Crear directorios necesarios
echo Creando directorios...
if not exist "storage\logs" mkdir "storage\logs"
if not exist "storage\framework" mkdir "storage\framework"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"

REM Configurar permisos
echo Configurando permisos...
icacls storage /grant "Todos:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Todos:(OI)(CI)F" /T

REM Alternativa si los comandos anteriores fallan
icacls storage /grant "Everyone:(OI)(CI)F" /T
icacls "bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T

REM Limpiar cachés
echo Limpiando cachés...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Generar key si no existe
echo Generando application key...
php artisan key:generate

echo.
echo Permisos configurados correctamente!
echo Ahora puedes ejecutar: php artisan migrate --seed
pause