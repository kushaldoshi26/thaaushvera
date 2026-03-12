FROM php:8.2-apache

# Install PHP extensions needed by Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev zip unzip git curl libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_sqlite pdo_mysql opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set Laravel storage permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions \
             storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Install PHP dependencies (no dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set up SQLite database
RUN mkdir -p /var/data \
    && if [ ! -f /var/data/database.sqlite ]; then touch /var/data/database.sqlite; fi \
    && chmod 664 /var/data/database.sqlite \
    && chown www-data:www-data /var/data/database.sqlite

# Configure Apache to serve Laravel's public folder
COPY docker/apache-laravel.conf /etc/apache2/sites-available/000-default.conf

# PHP production config
RUN echo "opcache.enable=1\nopcache.memory_consumption=128\nexpose_php=Off" \
    >> /usr/local/etc/php/conf.d/laravel.ini

# Entrypoint script to run migrations and start Apache
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
