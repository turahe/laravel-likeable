ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/composer-install.sh docker/test.sh /usr/local/bin/
RUN sed -i 's/\r$//' /usr/local/bin/composer-install.sh /usr/local/bin/test.sh \
    && chmod +x /usr/local/bin/composer-install.sh /usr/local/bin/test.sh

WORKDIR /app

CMD ["test.sh"]
