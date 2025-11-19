#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use EventManager\EventManager;

if ($argc !== 2) {
    echo "Usage: php import-event <Your Json file>\n";
    exit(1);
}

$filename = $argv[1];
$manager = new EventManager();
$result = $manager->process($filename);

$green = "\033[32m";
$red = "\033[31m";
$reset = "\033[0m";

$color = $result['success'] ? $green : $red;
echo $color . $result['message'] . $reset . "\n";
exit($result['success'] ? 0 : 1);
