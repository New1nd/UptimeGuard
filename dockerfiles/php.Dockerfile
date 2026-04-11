FROM php:8.3-fpm

ARG PROJECT_NAME
WORKDIR /var/www/${PROJECT_NAME}

RUN apt-get update \
  && apt-get install -y build-essential zlib1g-dev curl gnupg procps vim git unzip libzip-dev libsqlite3-dev libfcgi-bin \
  && docker-php-ext-install zip pdo pdo_sqlite \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# PHP-FPM healthcheck script
RUN echo '#!/bin/sh\n\
SCRIPT_NAME=/ping\n\
SCRIPT_FILENAME=/ping\n\
REQUEST_METHOD=GET\n\
cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1' > /usr/local/bin/php-fpm-healthcheck \
  && chmod +x /usr/local/bin/php-fpm-healthcheck

# PHP-FPM pool configuration
RUN echo '[www]\n\
pm = dynamic\n\
pm.max_children = 20\n\
pm.start_servers = 5\n\
pm.min_spare_servers = 3\n\
pm.max_spare_servers = 10\n\
pm.max_requests = 500\n\
ping.path = /ping\n\
ping.response = pong\n\
pm.status_path = /status' > /usr/local/etc/php-fpm.d/zz-custom.conf