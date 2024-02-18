ARG PHP_VERSION

FROM php:${PHP_VERSION}-fpm AS base

ARG TZ
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone

# hadolint ignore=DL3009
RUN apt-get update -yqq && apt-get install -yqq \
    git \
    make \
    wget \
    zip \
    libzip-dev \
    unzip \
    libpng-dev libjpeg-dev libwebp-dev

RUN docker-php-source extract \
    # ext-opache
    && docker-php-ext-enable opcache \
    # ext-postgresql
    && apt-get install -yqq libpq-dev \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pgsql \
    && apt-get remove libpq-dev -y \
    && docker-php-ext-install -j$(nproc) pcntl bcmath zip \
    && docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

ARG COMPOSER_VERSION
COPY docker/shared/scripts/install-composer.sh /tmp/scripts/install-composer.sh
RUN sh /tmp/scripts/install-composer.sh /usr/bin composer ${COMPOSER_VERSION}  \
    && composer --ansi --version --no-interaction

# php-fpm pool config
COPY docker/app-fpm/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/
COPY docker/app-fpm/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/

# php config
COPY docker/app-fpm/conf.d/app.ini  /usr/local/etc/php/conf.d/

COPY docker/shared/scripts/cleanup.sh /tmp/scripts/cleanup.sh


FROM base AS local

RUN apt-get install -yqq \
    sudo \
    nano \
    dnsutils \
    iproute2 \
    iputils-ping

ARG XDEBUG_VERSION
RUN pecl install xdebug-${XDEBUG_VERSION} \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete

COPY docker/app-fpm/conf.d/99-xdebug.ini /usr/local/etc/php/conf.d/
COPY docker/app-fpm/symfony /usr/local/bin/

RUN mkdir -p /var/www/.composer

ARG USER_UID
RUN usermod -u ${USER_UID} www-data
RUN chown -R 1000:1000 /var/www
RUN echo "www-data  ALL=NOPASSWD: ALL" >> /etc/sudoers
RUN /tmp/scripts/cleanup.sh

# entrypoint
COPY docker/shared/scripts/docker-entrypoint/resolve-docker-host-ip.sh /bin/docker-entrypoint/
ENTRYPOINT ["/bin/docker-entrypoint/resolve-docker-host-ip.sh"]
