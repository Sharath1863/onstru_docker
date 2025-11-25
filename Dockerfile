# Use official PHP FPM image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr.bin/composer

# Copy project files with correct ownership
COPY --chown=www-data:www-data . /var/www/html

# ←←← THIS IS THE KEY PART THAT WAS WRONG BEFORE ←←←
# storage and bootstrap/cache need 775 (or 777 in dev) and must be owned by www-data
RUN chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

# Optional but recommended: make the entire project readable
RUN find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \;

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]