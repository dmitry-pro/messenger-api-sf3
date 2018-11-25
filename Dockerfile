FROM php:7.1-fpm-alpine

LABEL maintainer="Dmitry Prokopenko zorkyysokol@gmail.com"

RUN apk add mysql-client
RUN apk add libpng libpng-dev libjpeg libjpeg-turbo-dev freetype-dev
RUN docker-php-ext-install pdo_mysql zip
RUN docker-php-ext-install mbstring && \
                docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/  &&  \
                docker-php-ext-install gd

# Use the default production configuration
#RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

# Override with custom opcache settings
#COPY php.ini $PHP_INI_DIR/conf.d/
COPY php.ini $PHP_INI_DIR/php.ini

EXPOSE 9000
