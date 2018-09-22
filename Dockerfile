FROM navidonskis/nginx-php7.1:latest

LABEL maintainer="Dmitry Prokopenko zorkyysokol@gmail.com"

VOLUME .:/var/www/application
VOLUME /run/php/php7.1-fpm.sock

RUN apt-get update
RUN apt-get install -y mysql-client

COPY php.ini /etc/php/7.1/fpm/
COPY php.ini /etc/php/7.1/cli/
