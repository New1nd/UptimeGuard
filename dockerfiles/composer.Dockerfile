FROM composer:2.9.5

ARG PROJECT_NAME
WORKDIR /var/www/${PROJECT_NAME}

ENTRYPOINT ["composer", "--ignore-platform-reqs"]
