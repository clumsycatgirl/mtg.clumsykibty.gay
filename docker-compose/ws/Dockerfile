FROM php:8.2-fpm-alpine
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli

CMD ["php", "/var/www/public/index.php", "startwebsocket"]
