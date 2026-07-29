#!/usr/bin/env php
<?php

declare(strict_types=1);

use Light\App\Service\FeedGenerator;

chdir(__DIR__ . '/../');

require 'vendor/autoload.php';

$container     = require 'config/container.php';
$feedGenerator = $container->get(FeedGenerator::class);

$count = $feedGenerator->write();

printf(
    "Done. %d article%s written to %s%s",
    $count,
    $count === 1 ? '' : 's',
    $feedGenerator->getFeedFile(),
    PHP_EOL
);