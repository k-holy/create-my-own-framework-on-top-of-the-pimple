<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Step 1
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
include_once realpath(__DIR__ . '/../vendor/autoload.php');

use Acme\Application;

$app = new Application();

// リクエスト変数を取得する
$app->findVar = $app->protect(function($key, $name, $default = null) {
    $value = null;
    switch ($key) {
    // $_GET
    case 'G':
        $value = (isset($_GET[$name])) ? $_GET[$name] : null;
        break;
    // $_POST
    case 'P':
        $value = (isset($_POST[$name])) ? $_POST[$name] : null;
        break;
    // $_COOKIE
    case 'C':
        $value = (isset($_COOKIE[$name])) ? $_COOKIE[$name] : null;
        break;
    // $_SERVER
    case 'S':
        $value = (isset($_SERVER[$name])) ? $_SERVER[$name] : null;
        break;
    }
    if (!isset($value) ||
        (is_string($value) && strlen($value) === 0) ||
        (is_array($value) && count($value) === 0)
    ) {
        $value = $default;
    }
    return $value;
});
// ?name=foo
// ?name=<script>alert('hello')</script>
?>
<html>
<body>
<h1>test</h1>
<p><?=$app->findVar('G', 'name')?></p>
</body>
</html>
