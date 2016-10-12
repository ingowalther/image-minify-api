<?php

use \IngoWalther\ImageMinifyApi\DependencyInjection\ContainerBuilder;
use \IngoWalther\ImageMinifyApi\Validator\RequestValidator;
use \Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build(realpath(__DIR__ . '/../'));

$app->post('/minify', function(Request $request) use ($container) {

    $validator = new RequestValidator();
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
