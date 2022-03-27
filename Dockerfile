FROM php:8.1-apache
COPY --from=library/composer:2.1.9 /usr/bin/composer /usr/local/bin/composer

ENV APACHE_DOCUMENT_ROOT /app/public

RUN apt-get update -y \
    && apt-get upgrade -y

# add git
RUN apt-get install -y git

# add PHP intl extension
RUN apt-get install -y zlib1g-dev libicu-dev g++ \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

# add zip
RUN apt-get install -y libzip-dev zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

# add xdebug
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

# clean
RUN apt-get clean -y && apt-get autoclean -y && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

# config apache server
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

RUN mkdir -m777 /app
WORKDIR /app