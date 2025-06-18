# EcoTrack Laravel Application Dockerfile (Fixed Supervisor)
FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    unzip \
    supervisor \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first (for better caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application files
COPY . .

# Complete composer installation
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy supervisor configuration
COPY docker/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf

# Create supervisor logs directory
RUN mkdir -p /var/log/supervisor

# Create a simple supervisor main config
RUN echo '[supervisord]\nnodaemon=true\nuser=root\n\n[unix_http_server]\nfile=/var/run/supervisor.sock\nchown=www-data:www-data\nchmod=0700\n\n[supervisorctl]\nserverurl=unix:///var/run/supervisor.sock\n\n[rpcinterface:supervisor]\nsupervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface\n\n[include]\nfiles = /etc/supervisor/conf.d/*.conf' > /etc/supervisor/supervisord.conf

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start supervisor in foreground
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]