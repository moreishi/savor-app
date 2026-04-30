FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    curl \
    sqlite \
    nginx \
    supervisor \
    nodejs \
    npm \
    linux-headers \
    oniguruma-dev \
    libxml2-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev

# PHP extensions
RUN docker-php-ext-install \
    mbstring \
    pdo \
    pdo_sqlite \
    xml \
    bcmath \
    gd \
    opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Build argument for APP_KEY
ARG APP_KEY
ENV APP_KEY=${APP_KEY}

COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev

COPY package.json package-lock.json ./
RUN npm ci && npm run build

COPY . .

# PHP config
COPY docker/php.ini /usr/local/etc/php/conf.d/savor.ini
COPY docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Generate production .env with only what's needed
RUN set -eux; \
    cp .env.example .env 2>/dev/null || true; \
    echo "APP_KEY=${APP_KEY}" >> .env; \
    php artisan storage:link; \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database

EXPOSE 80

CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
