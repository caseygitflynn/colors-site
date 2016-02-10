<?php

use Color\Color;
use Color\Image;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Slim\Slim([
    'view' => new \Slim\Views\Twig(),
    'templates.path' => dirname(__DIR__) . '/templates'
]);

$app->view()->parserExtensions = [
    new Slim\Views\TwigExtension()
];

$app->view()->parserOptions = [
    'debug' => true,
    'cache' => dirname(__DIR__) . '/cache'
];

$app->get('/image/random\.:ext', function ($extension) use ($app) {
    $color = Color::random_color();

    $width = $app->request()->get('w', 500);
    $height = $app->request()->get('h', 500);

    $image = new Image($color, $width, $height, $extension);

    $app->response->header('cache-control', 'private, max-age=0, no-cache');
    $app->response->header('Content-Type', $image->getContentType());
    $app->response->setBody($image->getImage());
});

$app->get('/image/:hexColor\.:ext', function ($hexColor, $extension) use ($app) {

    try {
        $color = new Color($hexColor);
        $width = $app->request()->get('w', 500);
        $height = $app->request()->get('h', 500);

        $image = new Image($color, $width, $height, $extension);

        $app->response->header('Content-Type', $image->getContentType());
        $app->response->setBody($image->getImage());

    } catch (InvalidArgumentException $e) {
        $app->response->setStatus(404);
        $app->response->setBody($e->getMessage());
    }
});

$app->run();
