@echo off
title SIGA - Sistema Web de Gestion Academica (UPDS)
echo ============================================================
echo   SIGA - Sistema Web de Gestion Academica (Laravel + XAMPP)
echo ============================================================
echo.

set PATH=D:\xampp\php;%PATH%

echo [1/3] Verificando MariaDB (XAMPP puerto 3307)...
netstat -ano | findstr ":3307 " | findstr "LISTENING" >nul
if errorlevel 1 (
    echo Iniciando MariaDB de XAMPP en puerto 3307...
    start "XAMPP MariaDB 3307" /MIN "D:\xampp\mysql\bin\mysqld.exe" --defaults-file="D:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 4 /nobreak >nul
) else (
    echo MariaDB ya se encuentra activo en el puerto 3307.
)

echo [2/3] Abriendo navegador en http://127.0.0.1:8000 ...
start "" http://127.0.0.1:8000

echo [3/3] Iniciando servidor Laravel en http://127.0.0.1:8000 ...
echo (Deje esta ventana abierta mientras usa el sistema. Presione Ctrl+C para detener.)
echo.
cd /d "d:\PROGRAMACION IV\PROYECTO_FINAL\sistema-gestion-academica"
"D:\xampp\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
pause
