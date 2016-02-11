<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Color\Color;
use Image\Image;

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

$app->get('/:hexColor\-:dimensions\.:ext', function ($hexColor, $dimensions, $extension) use ($app) {
    $dimensions = explode('x', $dimensions);

    if (count($dimensions) == 0) {
        $width = 500;
        $height = 500;
    }

    if (count($dimensions) == 1) {
        $width = $dimensions[0];
        $height = $width;
    }

    if (count($dimensions) == 2)
    {
        $width = $dimensions[0];
        $height = $dimensions[1];
    }

    try {
        $color = strtolower($hexColor) == 'random' ? Color::random_color() : new Color($hexColor);
        $image = new Image($color, $width, $height, $width . 'x' . $height, $extension);

        $app->response->header('Content-Type', $image->getContentType());
        $app->response->setBody($image->getImage());

    } catch (Exception $e) {
        $image = new Image(new Color("CCC"), 500, 500, $e->getMessage(), "png");
        $app->response->header('Content-Type', $image->getContentType());
        $app->response->setBody($image->getImage());
    }
});

$app->run();
