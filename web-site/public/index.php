<?php

require __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../container.php';
$container->get(\Web\App::class)->main();
