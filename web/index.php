<?php

use \Symfony\Component\DependencyInjection\ContainerBuilder;
use \Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use \Symfony\Component\Config\FileLocator;
use \IngoWalther\ImageMinifyApi\CompilerPass\CompressorPass;
use \IngoWalther\ImageMinifyApi\CompilerPass\CommandPass;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$container = new ContainerBuilder();
$container->addCompilerPass(new CompressorPass());
$container->addCompilerPass(new CommandPass());
$container->setParameter('basePath', realpath(__DIR__ . '/../'));
$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../config'));
$loader->load('services.yml');
$container->compile();

$app->post('/minify', function(\Symfony\Component\HttpFoundation\Request $request) use ($container) {

    $validator = new \IngoWalther\ImageMinifyApi\Validator\RequestValidator();
    $validator->validateRequest($request);

    $apiKeyCheck = $container->get('apiKeyCheck');
    $user = $apiKeyCheck->check($request->request->get('api_key'));

    $minify = $container->get('minify');
    $result = $minify->minify($request->files->get('image'), $user);

    return $result;
});

$app->error(function (\Exception $e, $code) use ($container) {
    $errorHandler = $container->get('errorHandler');
    return $errorHandler->handle($e, $code);
});

$app->run();
