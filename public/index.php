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

$app->get('/random', function () use ($app) {
    $color = Color::random_color();

    $app->response->header('cache-control', 'private, max-age=0, no-cache');
    $app->redirect('/' . $color->getHexColor(false));
});

$app->get('/image/random', function () use ($app) {
    $color = Color::random_color();

    $app->response->header('cache-control', 'private, max-age=0, no-cache');
    $app->redirect('/image/' . $color->getHexColor(false));
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
        $color = Color::random_color();
        $page["title"] = $e->getMessage();
        $page["color"] = $color->getHexColor();
        $page["text_color"] = $color->getContrastColor()->getHexColor();

        $app->response->setStatus(404);

        $app->render('color.twig', compact('page'));
    }
});

$app->get('/:hexColor', function ($hexColor) use ($app) {
    $page = [];

    try {
        $color = new Color($hexColor);
        $page["title"] = $color->getHexColor();
        $page["color"] = $color->getHexColor();
        $page["text_color"] = $color->getContrastColor()->getHexColor();
    } catch (InvalidArgumentException $e) {
        $color = Color::random_color();
        $page["title"] = $e->getMessage();
        $page["color"] = $color->getHexColor();
        $page["text_color"] = $color->getContrastColor()->getHexColor();

        $app->response->setStatus(404);
    }

    $app->render('color.twig', compact('page'));
});

$app->run();
