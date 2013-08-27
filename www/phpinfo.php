<?php
/**
 * Create my own framework on top of the Pimple
 *
 * phpinfo
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

$app->on('GET', function($app, $method) {
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    if (preg_match('~<body[^>]*>(.*)</body>~is', $phpinfo, $matches)) {
        $phpinfo = $matches[1];
    }
    return $app->render('phpinfo.html', array(
        'title'   => 'phpinfo',
        'phpinfo' => $phpinfo,
    ));
});

$app->run();
