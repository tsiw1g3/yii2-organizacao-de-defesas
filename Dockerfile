FROM yiisoftware/yii2-php:7.4-apache as build

WORKDIR /app
COPY . .

RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress --no-suggest --no-scripts --no-plugins

ENV BASE_URL=http://localhost:80
EXPOSE 80 3306