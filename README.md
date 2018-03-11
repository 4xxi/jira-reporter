Installation Instructions
==========

Everything below is a template for Installation Instructions. It should be updated with the full steps for setting up
your project.

## Requirements

* [Docker and Docker Compose](https://docs.docker.com/engine/installation)
* [MacOS Only]: Docker Sync (run `gem install docker-sync` to install it)

## Configuration

Application configuration is stored in `.env` file. 

### HTTP port
If you have nginx or apache installed and using 80 port on host system you can either stop them before proceeding or 
reconfigure Docker to use another port by changing value of `SERVER_HTTP_PORT` in `.env` file.

### Application environment
You can change application environment to `dev` of `prod` by changing `APP_ENV` variable in `.env` file.

### DB name and credentials
DB name and credentials could by reconfigured by changing variables with `POSTGRES` prefix in `.env` file. It is 
recommended to restart containers after changing these values (new database will be automatically created on containers 
start).

## Installation

### 1. Start Containers and install dependencies 
On Linux:
```bash
docker-compose up -d
```
On MacOS:
```bash
docker-sync-stack start
```
### 2. Run migrations, install fixtures
```bash
docker-compose exec php bin/console doctrine:migrations:migrate
```

### 3. Build frontend
Place instructions to build frontend here.

### 4. Open project
Just go to [http://localhost](http://localhost)


Application commands
==========
Add application-specific console commands and their description here.


Useful commands and shortcuts
==========

## Shortcuts
It is recommended to add short aliases for the following frequently used container commands:

* `docker-compose exec php php` to run php in container
* `docker-compose exec php composer` to run composer
* `docker-compose exec php bin/console` to run Symfony CLI commands
* `docker-compose exec db psql` to run PostgreSQL commands


## Checking code style and running tests
Fix code style by running PHP CS Fixer:
```bash
docker-compose exec php vendor/bin/php-cs-fixer fix
```

Run PHP Unit Tests:
```bash
docker-compose exec php bin/phpunit
```