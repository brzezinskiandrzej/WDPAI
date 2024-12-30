FROM php:7.4.3-fpm-alpine3.11

# Zainstaluj zależności wymagane dla PDO PostgreSQL i GD
RUN apk update && apk add --no-cache \
    libpq \
    postgresql-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    freetype-dev \
    zlib-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql gd

# Kopiuj kod aplikacji do obrazu
COPY . /app

# Ustaw wolumin, aby zachować dane aplikacji
VOLUME ["/app"]
