# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Docker-based development environment (LaraDock) for Laravel applications. The Laravel source code goes in the `src/` directory.

## Common Commands

### Start/Stop Services
```bash
docker-compose up -d          # Start all services
docker-compose down -v        # Stop and remove volumes
```

### Laravel Development (run from project root)
```bash
docker-compose run --rm composer install              # Install dependencies
docker-compose run --rm composer create-project laravel/laravel .  # New Laravel project
docker-compose run --rm artisan migrate               # Run migrations
docker-compose run --rm artisan <command>             # Any artisan command
docker-compose run --rm npm install                   # Install npm packages
docker-compose run --rm npm run dev                   # Run Vite dev server
```

### Container Access
```bash
docker exec -it <container_name> sh   # Shell into container
```

## Architecture

### Services (docker-compose.yaml)
- **nginx** - Web server on port 81, proxies PHP to php-fpm
- **php** - PHP 8.3-fpm with PostgreSQL/MySQL PDO extensions
- **db** - PostgreSQL 12 database
- **composer** - Runs composer commands (profile: tools)
- **artisan** - Runs artisan commands (profile: tools)
- **npm** - Node 20 for frontend builds (profile: tools)

### Configuration
- `.env` - Docker environment variables (PROJECT_NAME controls paths)
- `env/postgres.env` - Database credentials
- `nginx/nginx.conf` - Nginx server configuration
- `dockerfiles/` - Custom PHP and Composer Dockerfiles

### Volumes
- `src/` - Laravel application source (mounted to containers)
- `containers_data/` - Persistent data for db, pgadmin, redis
