@echo off
echo Lancement de l'application de gestion locative...
cd /d C:\gestion-locative

:: On lance les conteneurs.
:: L'option --build force Docker a verifier si le Dockerfile a change,
:: mais il ne reinstallera pas tout s'il n'y a pas de changement.
docker compose up -d

echo Attente du demarrage des services...
timeout /t 5 > nul

:: On ouvre le navigateur sur le port 8000 (celui de votre config)
start "" http://localhost:8000
