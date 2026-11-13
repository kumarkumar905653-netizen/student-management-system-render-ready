
FROM php:8.2-apache

RUN apt-get update && apt-get install -y ca-certificates \
    && update-ca-certificates \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

COPY docker/start-apache.sh /usr/local/bin/start-apache

RUN chmod +x /usr/local/bin/start-apache

EXPOSE 10000

CMD ["/usr/local/bin/start-apache"]
