# abfaltersbach.at

![Deploy website](https://github.com/biegl/web-abfaltersbach-at/workflows/Deploy%20website/badge.svg)
[![This project is using Percy.io for visual regression testing.](https://percy.io/static/images/percy-badge.svg)](https://percy.io/eda35841/Gemeinde-Abfaltersbach)

## Setup

The project uses Docker for local development. The following will build the app container based on the Dockerfile and spin up the app and mysql container. `composer install` install the project dependencies and creates the autoloader. `artisan key:generate` will create a local key.

```
$ docker run --rm \
    -v $(pwd):/opt \
    -w /opt \
    laravelsail/php80-composer:latest \
    composer install
$ sail up -d
$ sail artisan key:generate
$ sail artisan migrate
```

## Development

### Helper Commands for CLI

```
# Start container
sail up -d
sail down -v

# php
sail php --version

# artisan
sail artisan queue:work

# composer
sail composer require laravel/sanctum

# npm
sail npm run serve
sail npm run prod

# run tests
sail test

# shell
sail shell

# tinker
sail tinker
```

### Backend - EasyAdmin

The self-contained application is located in "resources/easyadmin"

```
npm run serve
```

## Deployment

### Preparation

Build assets for production

```
npm run prod
```

### Upload

The following command uses Deployer (https://deployer.org/) to upload the latest remote version from the current branch.
All optimizations (e.g. composer) will be performed during deployment. Check `config/deploy.php` for further details.

```
sail artisan deploy
```

The upload uses the basic strategy and follows following process:
https://github.com/lorisleiva/laravel-deployer/blob/master/docs/strategy-basic.md
