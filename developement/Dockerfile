FROM mythevalentinus/twitchbot

MAINTAINER Valentin Deville <contact@valentin-deville.eu>

EXPOSE 9000

RUN pecl install redis \
    && pecl install xdebug \
    && docker-php-ext-enable redis xdebug

