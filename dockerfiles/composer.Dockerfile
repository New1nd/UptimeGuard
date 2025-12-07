FROM composer:latest

ARG PROJECT_NAME
WORKDIR /var/www/laradock

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
