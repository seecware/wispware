FROM php:8.2-fpm

# --------------------------------------------------
# Dependencias del sistema
# --------------------------------------------------
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    iputils-ping \
    netcat-openbsd \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libssh2-1-dev \
    && rm -rf /var/lib/apt/lists/*

# --------------------------------------------------
# Extensiones PHP necesarias
# --------------------------------------------------
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        sockets

# --------------------------------------------------
# Composer (oficial)
# --------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------------------------------
# Directorio de trabajo
# --------------------------------------------------
WORKDIR /var/www/html

# --------------------------------------------------
# Permisos (opcional pero recomendado)
# --------------------------------------------------
RUN chown -R www-data:www-data /var/www

