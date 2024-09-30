FROM yiisoftware/yii2-php:7.4-apache-latest as build

WORKDIR /app
COPY . .
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --no-suggest --no-scripts --no-plugins
RUN composer dump-autoload -o

ENV BASE_URL=https://sistema-de-defesas-api.app.ic.ufba.br
ENV BASE_FRONTEND_URL=https://sistema-de-defesas.app.ic.ufba.br
EXPOSE 80