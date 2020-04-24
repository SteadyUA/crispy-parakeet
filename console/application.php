#!/usr/bin/env php
<?php
// application.php

require __DIR__ . '/vendor/autoload.php';
$container = require __DIR__ . '/container.php';

use Symfony\Component\Console\Application;

$application = new Application();

$taggedServices = $container->findTaggedServiceIds('Symfony\Component\Console\Command\Command');
foreach ($taggedServices as $id => $tags) {
    $application->add($container->get($id));
}

$application->run();
