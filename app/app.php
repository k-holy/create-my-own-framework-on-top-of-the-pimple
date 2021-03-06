<?php
/**
 * Create my own framework on top of the Pimple
 *
 * アプリケーション共通初期処理
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
include_once realpath(__DIR__ . '/../vendor/autoload.php');

use Acme\Application;
use Acme\Domain\Value\DateTime;
use Acme\Security\HashProcessor;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Volcanus\Configuration\Configuration;

use Volcanus\Error\ErrorFormatter;
use Volcanus\Error\ExceptionFormatter;
use Volcanus\Error\TraceFormatter;
use Volcanus\Error\StackTraceIterator;

use Volcanus\Database\Dsn;
use Volcanus\Database\DoctrineCacheFactory;
use Volcanus\Database\Driver\Pdo\PdoFactory;
use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\Driver\Pdo\PdoTransaction;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;
use Volcanus\Database\MetaData\Cache\DoctrineCacheProcessor;

use Volcanus\TemplateRenderer\Renderer;
use Volcanus\TemplateRenderer\Adapter\PhpTalAdapter;

$app = new Application();

//-----------------------------------------------------------------------------
// アプリケーション設定オブジェクトを生成
//-----------------------------------------------------------------------------
$app->config = function(Application $app) {
    $config = new Configuration(array(
        'debug'      => true,
        'app_id'     => 'acme',
        'app_root'   => __DIR__,
        'web_root'   => realpath(__DIR__ . '/../www'),
        'log_dir'    => __DIR__ . DIRECTORY_SEPARATOR . 'log',
        'log_file'   => null,
        'error_log'  => null,
        'error_view' => 'error.html',
        'timezone'   => 'Asia/Tokyo',
        'datetimeFormat' => 'Y/n/j H:i:s',
        'database'   => array(
            'dsn' => sprintf('sqlite:%s', __DIR__ . DIRECTORY_SEPARATOR . 'app.sqlite'),
            'meta_cache_dir' => realpath(__DIR__ . '/cache/db/meta'),
        ),
        'security' => array(
            'secret_key' => 'JoABXFuyYZ$3[@wmAyF8-ECx',
            'secret_salt' => 'mPg`UT]GnHtihLWH1bq5D&AW',
        ),
    ), Configuration::EXECUTE_CALLABLE);
    $config['log_file'] = function($config) use ($app) {
        return sprintf('%d-%02d.log', $app->clock->getYear(), $app->clock->getMonth());
    };
    $config['error_log'] = function($config) {
        return $config['log_dir'] . DIRECTORY_SEPARATOR . $config['log_file'];
    };
    return $config;
};

//-----------------------------------------------------------------------------
// Timezone
//-----------------------------------------------------------------------------
$app->timezone = function(Application $app) {
    return new \DateTimeZone($app->config->timezone);
};

//-----------------------------------------------------------------------------
// システム時計
//-----------------------------------------------------------------------------
$app->clock = function(Application $app) {
    return new DateTime(
        new \DateTime(sprintf('@%d', $_SERVER['REQUEST_TIME'])),
        ['timezone' => $app->timezone]
    );
};

//-----------------------------------------------------------------------------
// レンダラオブジェクト
//-----------------------------------------------------------------------------
$app->renderer = function(Application $app) {
    $renderer = new Renderer(new PhpTalAdapter(new \PHPTAL(), array(
        'outputMode'         => \PHPTAL::XHTML,
        'encoding'           => 'UTF-8',
        'templateRepository' => $app->config->web_root,
        'phpCodeDestination' => sys_get_temp_dir(),
        'forceReparse'       => true,
    )));
    // アプリケーション設定
    $renderer->assign('config', $app->config);
    return $renderer;
};

//-----------------------------------------------------------------------------
// ロガー
//-----------------------------------------------------------------------------
$app->logger = function(Application $app) {
    $app->logHandler = function() use ($app) {
        return new StreamHandler(
            $app->config->error_log,
            ($app->config->debug) ? Logger::DEBUG : Logger::NOTICE
        );
    };
    $logger = new Logger($app->config->app_id);
    $logger->pushHandler($app->logHandler);
    return $logger;
};

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
$app->errorFormatter = function(Application $app) {
    return new ErrorFormatter();
};

//-----------------------------------------------------------------------------
// 例外フォーマッタ
//-----------------------------------------------------------------------------
$app->exceptionFormatter = function(Application $app) {
    return new ExceptionFormatter();
};

//-----------------------------------------------------------------------------
// トレースフォーマッタ
//-----------------------------------------------------------------------------
$app->traceFormatter = function(Application $app) {
    return new TraceFormatter();
};

//-----------------------------------------------------------------------------
// スタックトレースイテレータ
//-----------------------------------------------------------------------------
$app->stackTrace = function(Application $app) {
    return new StackTraceIterator($app->traceFormatter);
};

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
// DSN
//-----------------------------------------------------------------------------
$app->dsn = function(Application $app) {
    return Dsn::createFromString($app->config->database->dsn);
};

//-----------------------------------------------------------------------------
// PDO
//-----------------------------------------------------------------------------
$app->pdo = function(Application $app) {
    return PdoFactory::createFromDsn($app->dsn);
};

//-----------------------------------------------------------------------------
// メタキャッシュ
//-----------------------------------------------------------------------------
$app->metaCache = function(Application $app) {
    $cache = DoctrineCacheFactory::create('phpFile', array(
        'directory' => $app->config->database->meta_cache_dir,
    ));
    return new DoctrineCacheProcessor($cache);
};

//-----------------------------------------------------------------------------
// データベースドライバ
//-----------------------------------------------------------------------------
$app->db = function(Application $app) {
    return new PdoDriver($app->pdo, new SqliteMetaDataProcessor($app->metaCache));
};

//-----------------------------------------------------------------------------
// データベーストランザクション
//-----------------------------------------------------------------------------
$app->transaction = function(Application $app) {
    return new PdoTransaction($app->pdo);
};

//-----------------------------------------------------------------------------
// ハッシュ処理クラス
//-----------------------------------------------------------------------------
$app->hashProcessor = function(Application $app) {
    return new HashProcessor(array(
        'algorithm' => 'sha256',
        'stretchingCount' => 100,
        'saltLength' => 64,
        'saltChars' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        'randomLength' => 10,
        'randomChars'  => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#%&+-./:=?[]_',
    ));
};

//-----------------------------------------------------------------------------
// エンティティオブジェクトファクトリ
//-----------------------------------------------------------------------------
$app->createEntity = $app->protect(function($name, array $properties = array()) use ($app) {
    $class = '\\Acme\\Domain\\Entity\\' . ucfirst($name);
    if (!class_exists($class, true)) {
        throw new \InvalidArgumentException(
            sprintf('The EntityObject "%s" is not found.', $name)
        );
    }
    return new $class($properties);
});

//-----------------------------------------------------------------------------
// バリューオブジェクトファクトリ
//-----------------------------------------------------------------------------
$app->createValue = $app->protect(function($name, $value, array $options = array()) use ($app) {
    $class = '\\Acme\\Domain\\Value\\' . ucfirst($name);
    if (!class_exists($class, true)) {
        throw new \InvalidArgumentException(
            sprintf('The ValueObject "%s" is not found.', $name)
        );
    }
    switch ($name) {
    case 'byte':
        if (!isset($options['decimals'])) {
            $options['decimals'] = 1;
        }
        break;
    case 'datetime':
        if (!isset($options['timezone'])) {
            $options['timezone'] = $app->clock->getTimezone();
        }
        if (!isset($options['format'])) {
            $options['format'] = $app->config->datetimeFormat;
        }
        break;
    }
    return new $class($value, $options);
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
