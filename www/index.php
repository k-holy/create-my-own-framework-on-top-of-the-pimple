<?php
/**
 * Create my own framework on top of the Pimple
 *
 * トップページ
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

$app->on('GET', function($app) {

    return $app->render('index.html', array(
        'title' => 'トップページ',
    ));

});

$app->run();
