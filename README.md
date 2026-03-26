# LaraDock

## Components:

- PHP
- NGINX
- POSTGRESQL
- REDIS
- REVERB
- NPM
- COMPOSER
- ARTISAN

## Setup

### 1. Configure hosts file

Add to `/etc/hosts` (Linux/Mac) or `C:\Windows\System32\drivers\etc\hosts` (Windows):
```
127.0.0.1 bb56land.local
```
Replace `bb56land` with your `PROJECT_NAME` from `.env`.

### 2. Start containers

Run ```docker-compose up -d```

Down ```docker-compose down -v```

List containers ```docker ps```

Stop all containers ```docker stop $(docker ps -aq) ```

Remove all containers ```docker rm $(docker ps -aq)  ```

Remove all images ```docker rmi $(docker images -q) ```

Remove all volumes ```docker volume prune -f```

Remove all volumes ```docker volume prune -f```

Open container bash ``` docker exec -it <<cont name>> sh```

```chown -R www-data:www-data```

```chmod -R 775```

=====================================================

```docker-compose run artisan ...```

```docker-compose run composer ...```

```docker-compose run composer create-project laravel/laravel .```

======================================================
# 1. Остановить и удалить всё
docker rm -f $(docker ps -aq) 2>/dev/null

# 2. Полная очистка
docker system prune -a --volumes -f

# 3. Дополнительно: если остались тома вручную
docker volume rm $(docker volume ls -q) 2>/dev/null

