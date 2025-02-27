FROM php:8.4-apache
WORKDIR /var/www/html
COPY . .

# Install PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libmagic-dev \
    && docker-php-ext-install pdo_sqlite fileinfo

RUN docker-php-ext-install pdo_sqlite fileinfo

# PHP config
COPY php.ini /usr/local/etc/php/php.ini

# APACHE WEBSERVER
ENV SERVER_NAME localhost
RUN echo "ServerName ${SERVER_NAME}" >> /etc/apache2/apache2.conf

# PORT
EXPOSE 80

RUN a2enmod rewrite
CMD ["apache2-foreground"]


