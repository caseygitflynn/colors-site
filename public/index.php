<?php

require dirname(__DIR__) . '/vendor/autoload.php';

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}

function getContrastYIQ($hexcolor){
    $r = hexdec(substr($hexcolor,0,2));
    $g = hexdec(substr($hexcolor,2,2));
    $b = hexdec(substr($hexcolor,4,2));
    $yiq = (($r*299)+($g*587)+($b*114))/1000;
    return ($yiq >= 128) ? 'black' : 'white';
}

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
    $page = [];

    $color = random_color();

    $app->redirect('/' . $color);
});

$app->get('/:color', function ($color) use ($app) {
    $page = [];

    if (preg_match("/([a-fA-F0-9]{3}){1,2}\b/", $color)) {
        $page["title"] = "#" . $color;
        $page["color"] = "#" . $color;
        $page["text_color"] = getContrastYIQ($color);
    } else {
        $randomColor = random_color();
        $page["title"] = "Invalid color #" . $color;
        $page["color"] = "#" . $randomColor;
        $page["text_color"] = getContrastYIQ($randomColor);

        $app->response->setStatus(404);
    }

    $app->render('color.twig', compact('page'));
});

$app->run();
