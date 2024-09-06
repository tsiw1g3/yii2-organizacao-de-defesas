FROM yiisoftware/yii2-php:7.4-apache-latest as build

WORKDIR /app
COPY . .
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN chmod 755 /app

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN echo "apc.enabled=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini \
    && echo "apc.shm_size=64M" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini \
    && echo "apc.ttl=7200" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini \
    && echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

RUN composer install --no-dev --prefer-dist --optimize-autoloader --apcu-autoloader --no-interaction --no-progress --no-suggest --no-scripts --no-plugins
RUN mkdir -p /tmp && chmod -R 0777 /tmp
RUN composer dump-autoload -o --apcu

ENV BASE_URL=https://sistema-de-defesas-api.app.ic.ufba.br
ENV BASE_FRONTEND_URL=https://sistema-de-defesas.app.ic.ufba.br
EXPOSE 3306