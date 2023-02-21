# MAIN
FROM php:8.2-apache

RUN apt-get update -qq \
    && apt-get install -yq zlib1g-dev g++ libicu-dev zip libzip-dev zip libpq-dev\
    git-all unzip libxml2-dev \
    --no-install-recommends\
    && pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install intl opcache\
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install soap \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql\
    && docker-php-ext-install mysqli

RUN docker-php-ext-enable xdebug
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy composer and vendor
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R u+rwx /var/www/html/

# Expose port
EXPOSE 80

# Run application
CMD [ "apache2-foreground" ]