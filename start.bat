@echo off
setlocal
title Gestion Locative - Force Start

:: 1. Configuration du dossier

echo [1/4] Nettoyage force des ports 8000 et 5173...

:: Tuer le processus sur le port 8000 (Laravel)
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING') do (
    echo Fermeture du processus %%a sur le port 8000...
    taskkill /f /pid %%a 2>nul
)

:: Tuer le processus sur le port 5173 (Vite)
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :5173 ^| findstr LISTENING') do (
    echo Fermeture du processus %%a sur le port 5173...
    taskkill /f /pid %%a 2>nul
)

echo [2/4] Lancement de Vite...
start /b cmd /c "npm run dev"

echo [3/4] Lancement de Laravel...
start /b cmd /c "php artisan serve"

echo [4/4] Ouverture du navigateur dans 5 secondes...
timeout /t 5 > nul
start http://localhost:8000

echo.
echo -------------------------------------------------------
echo Application LUXURIANCE relancee avec succes !
echo Les anciens processus ont ete forces a s'arreter.
echo -------------------------------------------------------
pause
