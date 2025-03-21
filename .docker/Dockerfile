# MAIN
FROM php:8.4-apache as base_symfony

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

WORKDIR /var/www/html

# Install php extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Make php extensions it executable
RUN chmod +x /usr/local/bin/install-php-extensions

# hadolint ignore=DL3008
RUN apt-get update && apt-get install -y --no-install-recommends \
	acl \
	file \
	gettext \
    git \
	&& rm -rf /var/lib/apt/lists/*

RUN set -eux; \
	install-php-extensions \
		@composer \
		apcu \
		intl \
		opcache \
		zip \
        pdo \
        pdo_pgsql \
        pgsql \
	;

RUN a2enmod rewrite && \
    chown -R www-data:www-data /var/www/html/ && \
    chmod -R u+rwx /var/www/html/ \
;

# Expose port
EXPOSE 80

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# Run application
CMD [ "apache2-foreground" ]

###> recipes ###
###< recipes ###

COPY --link webserver/conf.d/php.ini $PHP_INI_DIR/conf.d/

FROM base_symfony AS base_symfony_dev

ENV APP_ENV=dev XDEBUG_MODE=off

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
		xdebug \
	;

COPY --link webserver/conf.d/php.dev.ini $PHP_INI_DIR/conf.d/

RUN docker-php-ext-enable xdebug

# Run application
CMD [ "apache2-foreground" ]