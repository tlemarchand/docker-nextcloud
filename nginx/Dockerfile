FROM nginx:1.17

ARG NEXTCLOUD_VERSION

COPY nginx/php-fpm.conf /etc/nginx/conf.d/
COPY nginx/nextcloud.conf /etc/nginx/conf.d/

RUN apt-get update && apt-get install -y curl bzip2 && \
    mkdir /var/www && \
    rm /etc/nginx/conf.d/default.conf && \
    curl https://download.nextcloud.com/server/releases/nextcloud-${NEXTCLOUD_VERSION}.tar.bz2 \
    | tar -xjC /var/www/ && \
    apt-get remove --purge -y curl bzip2 && \
    apt-get autoremove --purge -y && \
    rm -rf /var/lib/apt/lists/* && \
    sed -i '/^user/d' /etc/nginx/nginx.conf && \
    touch /var/run/nginx.pid && \
    chown www-data: /var/run/nginx.pid && \
    chown -R www-data: /var/cache/nginx

USER www-data