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
use Acme\DateTime;

use Acme\Error\ErrorFormatter;
use Acme\Error\ExceptionFormatter;
use Acme\Error\TraceFormatter;
use Acme\Error\StackTraceIterator;

use Acme\Renderer\PhpTalRenderer;

use Acme\Database\Driver\Pdo\PdoDriver;
use Acme\Database\Driver\Pdo\PdoTransaction;
use Acme\Database\MetaDataProcessor\SqliteMetaDataProcessor;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$app = new Application();

//-----------------------------------------------------------------------------
// アプリケーション設定オブジェクトを生成
//-----------------------------------------------------------------------------
$app->config = $app->share(function(Application $app) {
    $config = new Configuration(array(
        'debug'      => true,
        'app_id'     => 'acme',
        'app_root'   => __DIR__,
        'web_root'   => realpath(__DIR__ . '/../www'),
        'log_dir'    => __DIR__ . DIRECTORY_SEPARATOR . 'log',
        'log_file'   => null,
        'error_log'  => null,
        'error_view' => 'error.html',
        'secret_key' => 'CCi:wYD-4:iV:@X%1zun[Y@:',
        'timezone'   => 'Asia/Tokyo',
        'database'   => array(
            'dsn' => sprintf('sqlite:%s', __DIR__ . DIRECTORY_SEPARATOR . 'app.sqlite'),
        ),
    ));
    $config['log_file'] = function($config) use ($app) {
        return sprintf('%d-%02d.log', $app->clock->year(), $app->clock->month());
    };
    $config['error_log'] = function($config) {
        return $config['log_dir'] . DIRECTORY_SEPARATOR . $config['log_file'];
    };
    return $config;
});

//-----------------------------------------------------------------------------
// システム時計
//-----------------------------------------------------------------------------
$app->clock = $app->share(function(Application $app) {
    $datetime = new DateTime(new \DateTime(sprintf('@%d', $_SERVER['REQUEST_TIME'])));
    $datetime->setTimeZone($app->config->timezone);
    return $datetime;
});

//-----------------------------------------------------------------------------
// レンダラオブジェクトを生成、グローバルなテンプレート変数をセット
//-----------------------------------------------------------------------------
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

//-----------------------------------------------------------------------------
// ロガー
//-----------------------------------------------------------------------------
$app->logger = $app->share(function(Application $app) {
    $app->logHandler = function() use ($app) {
        return new StreamHandler(
            $app->config->error_log,
            ($app->config->debug) ? Logger::DEBUG : Logger::NOTICE
        );
    };
    $logger = new Logger($app->config->app_id);
    $logger->pushHandler($app->logHandler);
    return $logger;
});

//-----------------------------------------------------------------------------
// ログ
//-----------------------------------------------------------------------------
$app->log = $app->protect(function($message, $level) use ($app) {
    return $app->logger->addRecord($level ?: Logger::INFO, $message);
});

//-----------------------------------------------------------------------------
// エラーページを返す
//-----------------------------------------------------------------------------
$app->errorView = $app->protect(function(\Exception $exception, $title = null, $message = null) use ($app) {
    return $app->renderer->fetch($app->config->error_view, array(
        'title'           => $title,
        'message'         => $message,
        'exception'       => $exception,
        'exception_class' => get_class($exception),
        'stackTrace'      => $app->stackTrace->initialize($exception->getTrace()),
    ));
});

//-----------------------------------------------------------------------------
// エラーフォーマッタ
//-----------------------------------------------------------------------------
$app->errorFormatter = $app->share(function(Application $app) {
    return new ErrorFormatter();
});

//-----------------------------------------------------------------------------
// 例外フォーマッタ
//-----------------------------------------------------------------------------
$app->exceptionFormatter = $app->share(function(Application $app) {
    return new ExceptionFormatter();
});

//-----------------------------------------------------------------------------
// トレースフォーマッタ
//-----------------------------------------------------------------------------
$app->traceFormatter = $app->share(function(Application $app) {
    return new TraceFormatter();
});

//-----------------------------------------------------------------------------
// スタックトレースイテレータ
//-----------------------------------------------------------------------------
$app->stackTrace = $app->share(function(Application $app) {
    return new StackTraceIterator($app->traceFormatter);
});

//-----------------------------------------------------------------------------
// エラーログ
//-----------------------------------------------------------------------------
$app->logError = $app->protect(function($level, $message, $file, $line) use ($app) {
    $app->log(
        $app->errorFormatter->format($level, $message, $file, $line),
        $app->errorLevelToLogLevel($level)
    );
});

//-----------------------------------------------------------------------------
// 例外ログ
//-----------------------------------------------------------------------------
$app->logException = $app->protect(function(\Exception $e) use ($app) {
    $app->log(
        $app->exceptionFormatter->format($e),
        ($e instanceof \ErrorException)
            ? $app->errorLevelToLogLevel($e->getSeverity())
            : Logger::CRITICAL
    );
});

//-----------------------------------------------------------------------------
// エラーレベルをログレベルに変換
//-----------------------------------------------------------------------------
$app->errorLevelToLogLevel = $app->protect(function($level) {
    switch ($level) {
    case E_USER_ERROR:
    case E_RECOVERABLE_ERROR:
        return Logger::ERROR;
    case E_WARNING:
    case E_USER_WARNING:
        return Logger::WARNING;
    case E_NOTICE:
    case E_USER_NOTICE:
        return Logger::NOTICE;
    case E_STRICT:
    case E_DEPRECATED:
    case E_USER_DEPRECATED:
    default:
        break;
    }
    return Logger::INFO;
});

//-----------------------------------------------------------------------------
// PDO
//-----------------------------------------------------------------------------
$app->pdo = $app->share(function(Application $app) {
    try {
        $pdo = new \PDO($app->config->database->dsn);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
        throw new \RuntimeException(
            sprintf('Invalid DSN: "%s"', $app->config->database->dsn)
        );
    }
    return $pdo;
});

//-----------------------------------------------------------------------------
// データベースドライバ
//-----------------------------------------------------------------------------
$app->db = $app->share(function(Application $app) {
    return new PdoDriver($app->pdo, new SqliteMetaDataProcessor());
});

//-----------------------------------------------------------------------------
// データベーストランザクション
//-----------------------------------------------------------------------------
$app->transaction = $app->share(function(Application $app) {
    return new PdoTransaction($app->pdo);
});

//-----------------------------------------------------------------------------
// ドメインデータファクトリ
//-----------------------------------------------------------------------------
$app->createData = $app->protect(function($name, $options = null) {
    $class = '\\Acme\\Domain\\Data\\' . $name;
    if (!class_exists($class, true)) {
        throw new \InvalidArgumentException(
            sprintf('The Domain Data "%s" is not found.', $name)
        );
    }
    return new $class($options);
});

//-----------------------------------------------------------------------------
// アプリケーション初期処理
//-----------------------------------------------------------------------------
$app->registerEvent('init');
$app->addHandler('init', function(Application $app) {

    // エラーハンドラを登録
    set_error_handler(function($errno, $errstr, $errfile, $errline) use ($app) {
        if (error_reporting() & $errno) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        }
        $app->logError($errno, $errstr, $errfile, $errline);
        return true;
    });

    // 例外ハンドラを登録
    set_exception_handler(function(\Exception $e) use ($app) {
        $app->logException($e);
        echo $app->errorView($e, null, $e->getMessage());
    });

});

return $app;
