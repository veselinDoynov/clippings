ARG PHP_VERSION

FROM objectsystems/php:${PHP_VERSION}
MAINTAINER Web Team <phpteam@objectsystems.com>

RUN apt-get update && apt-get install --allow-unauthenticated -y \
        curl \
        git \
        unzip \
        php${PHP_VERSION}-memcached \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/www && chown -R www-data: /var/www \
    && mkdir -p /var/app/current/storage/logs \
    && touch /var/app/current/storage/logs/laravel.log && chown -R www-data: /var/app/current/storage/logs
COPY cron.txt /root/cron.txt

RUN crontab -u www-data /root/cron.txt;

EXPOSE 9312
EXPOSE 9306
