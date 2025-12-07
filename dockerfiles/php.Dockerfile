FROM php:8.3-fpm

ARG PROJECT_NAME
WORKDIR /var/www/${PROJECT_NAME}

#RUN apt update \
#    && apt install postgresql-dev \
#    && docker-php-ext-install pgsql pdo_pgsql pdo

RUN apt-get update \
  && apt-get install -y postgresql build-essential zlib1g-dev default-mysql-client curl gnupg procps vim git unzip libzip-dev libpq-dev \
  && docker-php-ext-install zip pdo_mysql pdo_pgsql pgsql pdo