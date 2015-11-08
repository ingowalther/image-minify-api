<?php

use \Symfony\Component\DependencyInjection\ContainerBuilder;
use \Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use \Symfony\Component\Config\FileLocator;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$container = new ContainerBuilder();
$container->setParameter('basePath', realpath(__DIR__ . '/../'));
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
$loader->load('services.yml');

$app->post('/minify', function(\Symfony\Component\HttpFoundation\Request $request) use ($container) {
    $apiKeyCheck = $container->get('apiKeyCheck');
    $apiKeyCheck->check($request);

    $minify = $container->get('minify');
    $result = $minify->minify($request);

    return $result;
}) ;

$app->error(function (\Exception $e, $code) {
    $errorHandler = new \IngoWalther\ImageMinifyApi\Error\ErrorHandler();
    return $errorHandler->handle($e, $code);
});

$app->run();
