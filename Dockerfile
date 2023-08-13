FROM php:8.1-fpm-alpine

RUN apk --update --no-cache add \
  git \
  bash \
  libintl \
  icu-dev \
  icu-data-full \
  zlib-dev \
  libpng-dev \
  sqlite-dev \
  libzip-dev \
  libxml2-dev \
  libxslt-dev \
  libgomp \
  linux-headers\
  imagemagick imagemagick-dev \
  oniguruma-dev \
  openssh-client \
  rsync

RUN docker-php-ext-install sysvsem

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar \
  && chmod +x /usr/local/bin/composer

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app
COPY . /app

RUN composer install

CMD symfony server:start
