FROM php:8.3-cli

# Installer extensions nécessaires (SQLite, zip, etc.)
RUN apt-get update && apt-get install -y \
    sqlite3 libsqlite3-dev unzip zip curl git libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Installer Composer (récupéré depuis une autre image officielle)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail dans le conteneur
WORKDIR /var/www

# Copier tout le code source dans le conteneur
COPY . .

# Installer les dépendances PHP
RUN composer install
