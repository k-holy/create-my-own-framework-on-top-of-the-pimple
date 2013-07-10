<?php
/**
 * Create my own framework on top of the Pimple
 *
 * URLルーティング用ゲートウェイスクリプト
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include realpath(__DIR__ . '/../app/app.php');
$app->init();

use Volcanus\Routing\Router;
use Volcanus\Routing\Exception\NotFoundException;
use Volcanus\Routing\Exception\InvalidParameterException;

use Symfony\Component\HttpFoundation\Response;

$router = Router::instance(array(
    'searchExtensions' => 'php,html',
    'overwriteGlobals' => true,
));

$router->importGlobals();

try {
    $router->execute();
} catch (\Exception $exception) {
    $statusCode = 500;
    $message = null;
    if ($exception instanceof NotFoundException) {
        $statusCode = 404;
        $message = 'ページが見つかりません';
    } elseif ($exception instanceof InvalidParameterException) {
        $statusCode = 400;
        $message = 'リクエストが不正です';
    }
    $response = new Response($app->errorView($exception, null, $message), $statusCode);
    $response->send();
}
