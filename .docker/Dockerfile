ARG PHP_VERSION=7.4
ARG COMPOSER_VERSION=2.7.9

FROM composer:${COMPOSER_VERSION} AS composer
FROM php:${PHP_VERSION}-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app