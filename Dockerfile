FROM composer:2.7 AS vendor
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev

FROM php:8.3.0-fpm

USER root
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    openssl \
    nginx \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    zlib1g-dev \
    libzip-dev \
    libpq-dev \
    gcc \
    g++ \
    make \
    vim \
    unzip \
    curl \
    git \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    locales \
    libonig-dev \
    supervisor \
    libicu-dev \
    libgmp-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        ctype \
        fileinfo \
        gmp \
        intl \
    && pecl install -o -f redis \
    && pecl install apfd \
    && docker-php-ext-enable redis

RUN php -m

WORKDIR /srv/app

COPY . /srv/app

COPY --from=vendor /app/vendor/ /srv/app/vendor/

COPY ./dockerconfig/php/php.ini /usr/local/etc/php/conf.d/local.ini
COPY ./dockerconfig/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./dockerconfig/php-fpm/zzz-docker.conf /usr/local/etc/php-fpm.d/zzz-docker.conf
COPY ./dockerconfig/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./dockerconfig/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/log/supervisor && \
    mkdir -p /var/log/php-fpm && \
    touch /var/log/php_errors.log \
          /var/log/php-fpm-slow.log \
          /var/log/php-fpm-access.log \
          /var/log/php-fpm-errors.log && \
    chown -R www-data:www-data /var/log/php-fpm \
                               /var/log/php_errors.log \
                               /var/log/php-fpm-slow.log \
                               /var/log/php-fpm-access.log \
                               /var/log/php-fpm-errors.log && \
    chmod 640 /var/log/php-fpm

RUN chown -R www-data:www-data * && \
    chmod -R 775 /srv/app/storage && \
    php artisan storage:link

EXPOSE 80

RUN chmod +x /srv/app/post_deploy.sh

ENTRYPOINT ["/srv/app/post_deploy.sh"]
