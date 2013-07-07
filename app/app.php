<?php
/**
 * Create my own framework on top of the Pimple
 *
 * アプリケーション共通初期処理
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
include_once realpath(__DIR__ . '/../vendor/autoload.php');

use Acme\Application;
use Acme\Configuration;
use Acme\StackTraceIterator;
use Acme\Renderer\PhpTalRenderer;

$app = new Application();

// アプリケーション設定オブジェクトを生成
$app->config = $app->share(function(Application $app) {
    $config = new Configuration(array(
        'debug'      => true,
        'app_root'   => __DIR__,
        'web_root'   => realpath(__DIR__ . '/../www'),
        'log_dir'    => __DIR__ . DIRECTORY_SEPARATOR . 'log',
        'log_file'   => date('Y-m') . '.log',
        'error_log'  => null,
        'error_view' => 'error.html',
        'secret_key' => 'CCi:wYD-4:iV:@X%1zun[Y@:',
    ));
    $config['error_log'] = function($config) {
        return $config['log_dir'] . DIRECTORY_SEPARATOR . $config['log_file'];
    };
    return $config;
});

// レンダラオブジェクトを生成、グローバルなテンプレート変数をセット
$app->renderer = $app->share(function(Application $app) {
    $renderer = new PhpTalRenderer(array(
        'outputMode'         => \PHPTAL::XHTML,
        'encoding'           => 'UTF-8',
        'templateRepository' => $app->config->web_root,
        'phpCodeDestination' => sys_get_temp_dir(),
        'forceReparse'       => true,
    ));
    // アプリケーション設定
    $renderer->assign('config', $app->config);
    return $renderer;
});

// ログ
$app->log = $app->protect(function($level, $message) use ($app) {
    error_log(
        sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), $level, $message),
        3,
        $app->config->error_log
    );
});

// エラーページを返す
$app->errorView = $app->protect(function(\Exception $exception, $message = null) use ($app) {
    return $app->renderer->fetch($app->config->error_view, array(
        'title'           => 'エラーが発生しました',
        'message'         => $message,
        'exception'       => $exception,
        'exception_class' => get_class($exception),
        'stackTrace'      => $app->trace($exception->getTrace()),
    ));
});

// スタックトレースイテレータを返す
$app->trace = $app->protect(function(array $trace) {
    return new StackTraceIterator($trace);
});

// アプリケーション初期処理
$app->registerEvent('init');
$app->addHandler('init', function(Application $app) {
    error_reporting(E_ALL);
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    });
    set_exception_handler(function(\Exception $e) use ($app) {
        $app->log('ERROR', (string)$e);
        echo $app->errorView($e, $e->getMessage());
    });
});

return $app;
