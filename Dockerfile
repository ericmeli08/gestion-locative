# Dockerfile

FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git curl libzip-dev unzip sqlite3 libsqlite3-dev npm

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_sqlite zip


# Créer un utilisateur non-root
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel && \
    mkdir -p /home/laravel && chown -R laravel:laravel /home/laravel

# Définir le dossier de travail
WORKDIR /var/www

# Copier les fichiers
COPY . .
COPY .env .env

# Installer les dépendances Laravel et JS
RUN composer install
RUN npm install
RUN npm run build

# Créer le dossier database si absent
RUN mkdir -p database && \
    touch database/database.sqlite && \
    chown -R www-data:www-data database

# Donner les bonnes permissions
RUN chown -R laravel:www-data /var/www

RUN apt-get update && apt-get install -y cron



# Copier le fichier cron
COPY laravel-cron /etc/cron.d/laravel-cron

# Donner les permissions
RUN chmod 0644 /etc/cron.d/laravel-cron

# Appliquer le cron
RUN crontab /etc/cron.d/laravel-cron

# Lancer cron et PHP-FPM au démarrage
CMD ["sh", "-c", "cron && php-fpm"]


USER laravel
