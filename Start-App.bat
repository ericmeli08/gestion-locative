@echo off
echo Lancement de l'application de gestion locative...
cd /d C:\gestion-locative
start "" http://localhost:8000
timeout /t 6 > nul
docker compose up
