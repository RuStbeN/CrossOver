@echo off
echo ========================================
echo    CONFIGURACION DE PROYECTO LARAVEL
echo ========================================

REM Verificar si composer está instalado
composer --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Composer no está instalado o no está en el PATH
    pause
    exit /b 1
)

REM Instalar dependencias
echo 1. Instalando dependencias de Composer...
composer install

REM Crear archivo .env si no existe
if not exist ".env" (
    echo 2. Creando archivo .env...
    copy ".env.example" ".env"
) else (
    echo 2. Archivo .env ya existe
)

REM Crear directorios necesarios
echo 3. Creando directorios necesarios...
if not exist "storage\logs" mkdir "storage\logs"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "bootstrap\cache" mkdir "bootstrap\cache"

REM Configurar permisos
echo 4. Configurando permisos...
icacls storage /grant "Todos:(OI)(CI)F" /T >nul 2>&1
icacls "bootstrap\cache" /grant "Todos:(OI)(CI)F" /T >nul 2>&1
icacls storage /grant "Everyone:(OI)(CI)F" /T >nul 2>&1
icacls "bootstrap\cache" /grant "Everyone:(OI)(CI)F" /T >nul 2>&1

REM Limpiar cachés
echo 5. Limpiando cachés...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

REM Generar key
echo 6. Generando application key...
php artisan key:generate

REM Crear tablas de sesiones si usa database
echo 7. Creando tabla de sesiones...
php artisan session:table

echo.
echo ========================================
echo    CONFIGURACION COMPLETADA
echo ========================================
echo.
echo Ahora puedes ejecutar:
echo   php artisan migrate --seed
echo   php artisan serve
echo.
pause