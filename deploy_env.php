<?php

namespace Deployer;

require __DIR__.'/vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__.'/')->load();

desc('Inject all necessary .env variables inside deployer config');
task('deploy:env', function () {
    set('deployer_application', env('APP_NAME'));
    set('deployer_ssh_port', env('DEPLOYER_SSH_PORT'));
    set('deployer_remote_user', env('DEPLOYER_REMOTE_USER'));
    set('deployer_hostname', env('DEPLOYER_HOSTNAME'));
    set('deployer_deploy_path', env('DEPLOYER_DEPLOY_PATH'));
    set('deployer_bin_php', env('DEPLOYER_BIN_PHP'));
    set('deployer_bin_composer', env('DEPLOYER_BIN_COMPOSER'));
});
