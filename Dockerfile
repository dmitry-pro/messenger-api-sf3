FROM navidonskis/nginx-php7.1:latest

LABEL maintainer="Dmitry Prokopenko zorkyysokol@gmail.com"

RUN apt-get update
RUN apt-get install -y mysql-client
RUN sed -i "s/listen =.*/listen = 0.0.0.0:9000/" /etc/php/7.1/fpm/pool.d/www.conf
RUN service php7.1-fpm restart

COPY php.ini /etc/php/7.1/fpm/
COPY php.ini /etc/php/7.1/cli/

EXPOSE 9000
