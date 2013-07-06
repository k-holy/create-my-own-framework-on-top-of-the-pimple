<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ('/' === $path || !realpath(__DIR__ . $path)) {
    require __DIR__ . DIRECTORY_SEPARATOR . '__gateway.php';
} else {
    return false;
}
