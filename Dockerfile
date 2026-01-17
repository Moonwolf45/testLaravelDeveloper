FROM php:8.3-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание директории приложения
WORKDIR /var/www/html

# Сначала копируем ВЕСЬ код приложения
COPY . .

# Делаем env для докер главным
COPY .env.docker .env

# Генерируем ключ приложения
RUN php artisan key:generate

# Только потом устанавливаем зависимости
RUN composer install --optimize-autoloader --no-interaction

# Установка прав
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose порт 9000 для PHP-FPM
EXPOSE 9000

# Команда для запуска PHP-FPM
CMD ["php-fpm"]
