FROM php:8.2-apache

LABEL maintainer="Danish Nazir"

RUN apt-get update && \
    apt-get install -y --no-install-recommends libpq-dev && \
    docker-php-ext-install pgsql pdo_pgsql && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY src/ /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
