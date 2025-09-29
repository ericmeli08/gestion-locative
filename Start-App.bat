@echo off
echo Lancement de l'application de gestion locative...
cd /d C:\gestion-locative
start "" http://localhost:80001
timeout /t 6 > nul
docker compose up
