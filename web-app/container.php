<?php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$projectDir = __DIR__;
$containerBuilder = new ContainerBuilder();
$loader = new YamlFileLoader($containerBuilder, new FileLocator($projectDir));
$loader->load('services.yaml');
$containerBuilder->setParameter('project_dir', $projectDir);
$containerBuilder->setParameter('var_dir', $projectDir . '/var');
$containerBuilder->compile();

return $containerBuilder;
