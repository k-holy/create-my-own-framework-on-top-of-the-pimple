<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Step 2
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

// HTMLエスケープ
$app->escape = $app->protect(function($value, $default = '') {
    $map = function($filter, $value) use (&$map) {
        if (is_array($value) || $value instanceof \Traversable) {
            $results = array();
            foreach ($value as $val) {
                $results[] = $map($filter, $val);
            }
            return $results;
        }
        return $filter($value);
    };
    return $map(function($value) use ($default) {
        $value = (string)$value;
        if (strlen($value) > 0) {
            return htmlspecialchars($value, ENT_QUOTES);
        }
        return $default;
    }, $value);
});
// ?name=<script>alert('hello')</script>
// ?name[]=foo&name[]=<script>alert('hello!')</script>
$name = $app->findVar('G', 'name');
?>
<html>
<body>
<h1>test</h1>
<?php if (is_array($name)) : ?>
<?php foreach ($name as $_name) : ?>
<p><?=$app->escape($_name)?></p>
<?php endforeach ?>
<?php else : ?>
<p><?=$app->escape($name)?></p>
<?php endif ?>
</body>
</html>
