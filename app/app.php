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
use Acme\Renderer\PhpTalRenderer;
use Acme\Exception\HttpException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

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
    // $app
    $renderer->assign('app', $app);
    // $_SERVER
    $renderer->assign('server', $app->request->server->all());
    return $renderer;
});

// レンダラオブジェクトからのテンプレート出力でレスポンスを生成
$app->render = $app->protect(function($view, array $data = array(), $statusCode = 200, $headers = array()) use ($app) {
    return new Response($app->renderer->fetch($view, $data), $statusCode, $headers);
});

// HTTPエラーを返す
$app->abort = $app->protect(function($statusCode = 500, $message = null, $headers = array()) use ($app) {
    throw new HttpException($statusCode, $headers, $message);
});

// リダイレクトレスポンスを生成
$app->redirect = $app->protect(function($url, $statusCode = 303, $headers = array()) use ($app) {
    return new RedirectResponse(
        (false === strpos($url, '://'))
            ? $app->request->getSchemeAndHttpHost() . $url
            : $url,
        $statusCode, $headers
    );
});

// リクエストオブジェクトを生成
$app->request = $app->share(function(Application $app) {
    return Request::createFromGlobals();
});

// セッションオブジェクトを生成
$app->session = $app->share(function(Application $app) {
    return new Session(
        new NativeSessionStorage(
            array(
                'use_only_cookies' => 1,
                'cookie_httponly'  => 1,
                'entropy_length'   => 2048,
                'hash_function'    => 1,
                'hash_bits_per_character' => 5,
            ),
            new NativeFileSessionHandler($app->config->app_root . DIRECTORY_SEPARATOR . 'session')
        )
    );
});

// リクエスト変数を取得する
$app->findVar = $app->protect(function($key, $name, $default = null) use ($app) {
    $value = null;
    switch ($key) {
    // $_GET
    case 'G':
        $value = $app->request->query->get($name);
        break;
    // $_POST
    case 'P':
        $value = $app->request->request->get($name);
        break;
    // $_COOKIE
    case 'C':
        $value = $app->request->cookies->get($name);
        break;
    // $_SERVER
    case 'S':
        $value = $app->request->server->get($name);
        break;
    }
    if (isset($value)) {
        $value = $app->normalize($value);
    }
    if (!isset($value) ||
        (is_string($value) && strlen($value) === 0) ||
        (is_array($value) && count($value) === 0)
    ) {
        $value = $default;
    }
    return $value;
});

// リクエスト変数の正規化
$app->normalize = $app->protect(function($value) use ($app) {
    $filters = array(
        // HT,LF,CR,SP以外の制御コード(00-08,11,12,14-31,127,128-159)を除去
        // ※参考 http://en.wikipedia.org/wiki/C0_and_C1_control_codes
        function($val) {
            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]|\xC2[\x80-\x9F]/S', '', $val);
        },
        // 改行コードを統一
        function($val) {
            return str_replace("\r", "\n", str_replace("\r\n", "\n", $val));
        },
    );
    foreach ($filters as $filter) {
        $value = $app->map($filter, $value);
    }
    return $value;
});

// HTMLエスケープ
$app->escape = $app->protect(function($value, $default = '') use ($app) {
    return $app->map(function($value) use ($default) {
        $value = (string)$value;
        if (strlen($value) > 0) {
            return htmlspecialchars($value, ENT_QUOTES);
        }
        return $default;
    }, $value);
});

// 全ての要素に再帰処理
$app->map = $app->protect(function($filter, $value) use ($app) {
    if (is_array($value) || $value instanceof \Traversable) {
        $results = array();
        foreach ($value as $val) {
            $results[] = $app->map($filter, $val);
        }
        return $results;
    }
    return $filter($value);
});

// アプリケーションへのリクエストハンドラ登録
$app->on = $app->protect(function($allowableMethod, $function) use ($app) {
    $allowableMethods = explode('|', $allowableMethod);
    $handler = $app->protect(function(Application $app, $method) use ($function) {
        return $function($app, $method);
    });
    if (in_array('GET', $allowableMethods)) {
        $app->onGet = $handler;
    }
    if (in_array('POST', $allowableMethods)) {
        $app->onPost = $handler;
    }
    if (in_array('PUT', $allowableMethods)) {
        $app->onPut = $handler;
    }
    if (in_array('DELETE', $allowableMethods)) {
        $app->onDelete = $handler;
    }
});

// アプリケーション実行
$app->run = $app->protect(function() use ($app) {
    error_reporting(E_ALL);
    set_error_handler(function($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
    });

    // セッション開始
    $app->session->start();

    try {
        $method = $app->request->getMethod();
        $handlerName = 'on' . ucfirst(strtolower($method));
        if (!$app->offsetExists($handlerName)) {
            throw new HttpException(405);
        }
        $response = $app->{$handlerName}($app, $method);
    } catch (\Exception $e) {
        error_log(sprintf("[%s] %s\n", date('Y-m-d H:i:s'), (string)$e), 3, $app->config->error_log);
        $statusCode = 500;
        $statusMessage = null;
        $message = null;
        $headers = array();
        if ($e instanceof HttpException) {
            $statusCode = $e->getCode();
            $statusMessage = $e->getStatusMessage();
            $message = $e->getMessage();
            $headers = $e->getHeaders();
        }
        $response = $app->render($app->config->error_view,
            array(
                'title' => 'エラーが発生しました',
                'statusMessage' => $statusMessage,
                'message' => $message,
                'exception' => $e,
                'exception_class' => get_class($e),
            ),
            $statusCode,
            $headers
        );
    }
    $response->send();
});

return $app;
