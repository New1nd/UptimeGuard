FROM composer:latest

ARG PROJECT_NAME
WORKDIR /var/www/${PROJECT_NAME}

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
