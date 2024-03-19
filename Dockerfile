FROM yiisoftware/yii2-php:7.4-apache as build

WORKDIR /app
COPY . .

RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --no-suggest --no-scripts --no-plugins

ENV BASE_URL=https://sistema-de-defesas-api.app.ic.ufba.br
ENV BASE_FRONTEND_URL=https://sistema-de-defesas.app.ic.ufba.br
EXPOSE 80 3306