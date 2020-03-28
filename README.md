## Setup

The project uses Docker for local development. The following will build the app container based on the Dockerfile and spin up the app and mysql container. `composer install` install the project dependencies and creates the autoloader. `artisan key:generate` will create a local key.

```
$ docker-compose build && docker-compose up -d && docker-compose logs -f
$ ./composer install
$ ./php-artisan key:generate
```

## Development

### Helper Commands for CLI

```
# Run composer inside the app container
./composer

# Run bash inside the app container
./container

# Run mysql inside the database container
./db

# Run php unit tests
./phpunit

# Run artisan command inside the app container
./php-artisan
```

## Deployment

### Preparation

### Upload

The following command uses Deployer (https://deployer.org/) to upload the latest remote version from the current branch.
All optimizations (e.g. composer) will be performed during deployment. Check `config/deploy.php` for further details.

```
./php-artisan deploy
```

The upload uses the basic strategy and follows following process:
https://github.com/lorisleiva/laravel-deployer/blob/master/docs/strategy-basic.md
