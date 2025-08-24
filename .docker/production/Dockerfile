FROM php:8.3-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nano \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    libwebp-dev \
    curl \
    # libpcre3 \
    # libpcre3-dev \
    librdkafka-dev \
    libxml2-dev \
    supervisor \
    && docker-php-ext-install pdo_pgsql pgsql zip pcntl dom

# PHP upload limits
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini

# Install GD extension with WebP support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd gmp

# Install PHP extensions (redis, rdkafka, bcmath, pdo_mysql)
RUN pecl install redis rdkafka \
    && docker-php-ext-enable redis rdkafka \
    && docker-php-ext-install bcmath pdo_mysql

# Install MongoDB PHP Extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install OpenSwoole extension
RUN pecl install openswoole \
    && docker-php-ext-enable openswoole

# Increase PHP memory limit for Composer
RUN echo 'memory_limit = -1' > /usr/local/etc/php/conf.d/memory-limit.ini

# Copy Composer.phar from local project root
COPY .docker/composer.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer

# Copy only composer files first (to leverage Docker caching)
COPY composer.json ./

# Run composer install without autoload (to prevent errors)
RUN composer install --no-autoloader --no-scripts --no-dev

# Copy all project files
COPY . .

# Copy .env.example to .env
RUN cp .env.example .env

# Now generate optimized autoload files
RUN composer dump-autoload --optimize

# Set proper permissions
RUN chmod -R 755 storage bootstrap/cache

# Generate Key
Run php artisan key:generate

# Copy Supervisor config
COPY .docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# artisan commands
COPY .docker/production/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 8000 for OpenSwoole
EXPOSE 8000

# Use the entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Start Supervisor
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
