<?php

namespace Deployer;

require_once __DIR__ . '/../vendor/deployer/deployer/recipe/common.php';

desc('Configure composer for private repository');
task('composer:config', function () {
    run("{{bin/composer}} config --file {{release_path}}/composer.json http-basic.nova.laravel.com {{nova_username}} {{nova_password}}");
    writeln('<info>Composer config set</info>');
});
