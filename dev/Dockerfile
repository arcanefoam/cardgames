FROM php:8.2-fpm
 
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip
 
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
 
# Install PHP extensions
RUN docker-php-ext-install mbstring exif pcntl bcmath gd intl
RUN docker-php-ext-configure zip \
    && docker-php-ext-install zip
  
# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
 
# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
# Set working directory
WORKDIR /var/www/html