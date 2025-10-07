FROM composer:latest

WORKDIR /var/www/project

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
