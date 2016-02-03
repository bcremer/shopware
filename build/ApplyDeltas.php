#!/usr/bin/env php
<?php

set_time_limit(0);
date_default_timezone_set('UTC');
error_reporting(-1);

require __DIR__ . '/../autoload.php';
require 'functions.php';

$environment = getenv('SHOPWARE_ENV') ?: 'production';

$kernel = new Shopware\Kernel($environment, $environment !== 'production');
$config = $kernel->getConfig();
$dbConfig = $config['db'];

$connection = createConnection($dbConfig);

$shopPath = realpath(__DIR__ . '/../');

$modeArg = getopt('', array('mode:'));

if (!isset($modeArg['mode']) || $modeArg['mode'] == 'install') {
    $mode = \Shopware\Components\Migrations\AbstractMigration::MODUS_INSTALL;
} else {
    $mode = \Shopware\Components\Migrations\AbstractMigration::MODUS_UPDATE;
}

$migrationManger = new Shopware\Components\Migrations\Manager($connection, $shopPath . '/_sql/migrations');
$migrationManger->run($mode);

exit(0);
