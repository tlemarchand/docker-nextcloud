FROM php:8.0-fpm-bullseye

ARG NEXTCLOUD_VERSION

RUN set -ex; \
    \
    savedAptMark="$(apt-mark showmanual)"; \
    \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libevent-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libldap2-dev \
        libmcrypt-dev \
        libmemcached-dev \
        libpng-dev \
        libxml2-dev \
        libmagickwand-dev \
        libzip-dev \
        libwebp-dev \
        libgmp-dev \
    ; \
    \
    debMultiarch="$(dpkg-architecture --query DEB_BUILD_MULTIARCH)"; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-configure ldap --with-libdir="lib/$debMultiarch"; \
    docker-php-ext-install -j "$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        ldap \
        opcache \
        pcntl \
        pdo_mysql \
        zip \
        gmp \
    ; \
    \
# pecl will claim success even if one install fails, so we need to perform each install separately
    pecl install APCu-5.1.21; \
    pecl install memcached-3.1.5; \
    pecl install redis-5.3.4; \
    pecl install imagick-3.5.1; \
    \
    docker-php-ext-enable \
        apcu \
        memcached \
        redis \
        imagick \
    ; \
    \
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
    apt-mark auto '.*' > /dev/null; \
    apt-mark manual $savedAptMark; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
        | awk '/=>/ { print $3 }' \
        | sort -u \
        | xargs -r dpkg-query -S \
        | cut -d: -f1 \
        | sort -u \
        | xargs -rt apt-mark manual; \
    \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/*

RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.save_comments=1'; \
        echo 'opcache.revalidate_freq=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini; \
    echo 'apc.enable_cli=1' >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini; \
    echo 'memory_limit=512M' > /usr/local/etc/php/conf.d/memory-limit.ini; \
    sed -i 's/9000/\/run\/php-fpm\/www.sock/' /usr/local/etc/php-fpm.d/zz-docker.conf; \
    echo "pm.max_children = 20" >> /usr/local/etc/php-fpm.d/zz-docker.conf; \
    mkdir /run/php-fpm

RUN curl https://download.nextcloud.com/server/releases/nextcloud-${NEXTCLOUD_VERSION}.tar.bz2 \
    | tar -xjC /var/www/ && \
    rm -rf /var/www/nextcloud/updater && \
    mkdir /var/www/nextcloud/custom_apps && \
    mkdir /var/www/data && \
    mkdir /usr/share/templates/ && \
    ln -s /var/www/data/config.php /var/www/nextcloud/config/config.php && \
    chown www-data /var/www/nextcloud/config/CAN_INSTALL

COPY php-fpm/config/* /var/www/nextcloud/config/
COPY php-fpm/php-config/* /usr/local/etc/php/conf.d/

VOLUME /var/www/data
VOLUME /var/www/nextcloud/custom_apps

USER www-data

CMD php /var/www/nextcloud/occ upgrade && exec php-fpm