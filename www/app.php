<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Web共通初期処理
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include realpath(__DIR__ . '/../app/app.php');

use Acme\Application;
use Acme\DataObject;
use Acme\Exception\HttpException;

use Monolog\Logger;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

//-----------------------------------------------------------------------------
// セッションオブジェクトを生成
//-----------------------------------------------------------------------------
$app->session = $app->share(function(Application $app) {
    return new Session(
        new NativeSessionStorage(
            array(
                'use_only_cookies' => 1,
                'cookie_httponly'  => 1,
                'entropy_length'   => 2048,
                'entropy_file'     => '/dev/urandom',
                'hash_function'    => 1,
                'hash_bits_per_character' => 5,
            ),
            new NativeFileSessionHandler($app->config->app_root . DIRECTORY_SEPARATOR . 'session')
        )
    );
});

//-----------------------------------------------------------------------------
// フラッシュメッセージ
//-----------------------------------------------------------------------------
$app->flash = $app->share(function(Application $app) {
    return new DataObject(array(
        'add' => function($name, $message) use ($app) {
            $app->session->getFlashBag()->add($name, $message);
        },
        'has' => function($name) use ($app) {
            return $app->session->getFlashBag()->has($name);
        },
        'get' => function($name) use ($app) {
            return $app->session->getFlashBag()->get($name);
        },

        // Error
        'addError' => function($message) use ($app) {
            $app->flash->add('error', $message);
        },
        'hasError' => function() use ($app) {
            return $app->flash->has('error');
        },
        'getError' => function() use ($app) {
            return $app->flash->get('error');
        },

        // Alert
        'addAlert' => function($message) use ($app) {
            $app->flash->add('alert', $message);
        },
        'hasAlert' => function() use ($app) {
            return $app->flash->has('alert');
        },
        'getAlert' => function() use ($app) {
            return $app->flash->get('alert');
        },

        // Success
        'addSuccess' => function($message) use ($app) {
            $app->flash->add('success', $message);
        },
        'hasSuccess' => function() use ($app) {
            return $app->flash->has('success');
        },
        'getSuccess' => function() use ($app) {
            return $app->flash->get('success');
        },

        // Info
        'addInfo' => function($message) use ($app) {
            $app->flash->add('info', $message);
        },
        'hasInfo' => function() use ($app) {
            return $app->flash->has('info');
        },
        'getInfo' => function() use ($app) {
            return $app->flash->get('info');
        },
    ));
});

//-----------------------------------------------------------------------------
// トークンオブジェクトを生成
//-----------------------------------------------------------------------------
$app->token = $app->share(function(Application $app) {
    return new DataObject(array(
        'name'  => function() use ($app) {
            return sha1($app->config->secret_key);
        },
        'value' => function() use ($app) {
            return $app->session->getId();
        },
        'validate' => function($value) use ($app) {
            if (is_null($value)) {
                return false;
            }
            return ($value === $app->token->value());
        },
    ));
});

//-----------------------------------------------------------------------------
// CSRFトークンの検証
//-----------------------------------------------------------------------------
$app->csrfVerify = $app->protect(function($method) use ($app) {
    return $app->token->validate($app->findVar($method, $app->token->name()));
});

//-----------------------------------------------------------------------------
// リクエストオブジェクトを生成
//-----------------------------------------------------------------------------
$app->request = $app->share(function(Application $app) {
    return Request::createFromGlobals();
});

//-----------------------------------------------------------------------------
// リクエストURIを返す
//-----------------------------------------------------------------------------
$app->requestUri = $app->protect(function() use ($app) {
    return $app->request->getRequestUri();
});

//-----------------------------------------------------------------------------
// レンダラオブジェクトからのテンプレート出力でレスポンスを生成
//-----------------------------------------------------------------------------
$app->render = $app->protect(function($view, array $data = array(), $statusCode = 200, $headers = array()) use ($app) {
    return new Response(
        $app->renderer->fetch($view, $data),
        $statusCode,
        $headers
    );
});

//-----------------------------------------------------------------------------
// リダイレクトレスポンスを生成
//-----------------------------------------------------------------------------
$app->redirect = $app->protect(function($url, $statusCode = 303, $headers = array()) use ($app) {
    return new RedirectResponse(
        (false === strpos($url, '://'))
            ? $app->request->getSchemeAndHttpHost() . $url
            : $url,
        $statusCode,
        $headers
    );
});

//-----------------------------------------------------------------------------
// エラーページ用レスポンスを生成
//-----------------------------------------------------------------------------
$app->error = $app->protect(function(\Exception $exception) use ($app) {
    $statusCode = 500;
    $headers = array();
    $title = null;
    $message = null;
    if ($exception instanceof HttpException) {
        $statusCode = $exception->getCode();
        $headers = $exception->getHeaders();
        $message = $exception->getMessage();
        $title = $exception->getReasonPhrase();
    }
    return new Response(
        $app->errorView($exception, $title, $message),
        $statusCode,
        $headers
    );
});

//-----------------------------------------------------------------------------
// HTTPエラーを返す
//-----------------------------------------------------------------------------
$app->abort = $app->protect(function($statusCode = 500, $message = null, $headers = array()) use ($app) {
    throw new HttpException($statusCode, $headers, $message);
});

//-----------------------------------------------------------------------------
// リクエスト変数を取得する
//-----------------------------------------------------------------------------
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

//-----------------------------------------------------------------------------
// リクエスト変数の正規化
//-----------------------------------------------------------------------------
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

//-----------------------------------------------------------------------------
// HTMLエスケープ
//-----------------------------------------------------------------------------
$app->escape = $app->protect(function($value, $default = '') use ($app) {
    return $app->map(function($value) use ($default) {
        $value = (string)$value;
        if (strlen($value) > 0) {
            return htmlspecialchars($value, ENT_QUOTES);
        }
        return $default;
    }, $value);
});

//-----------------------------------------------------------------------------
// 全ての要素に再帰処理
//-----------------------------------------------------------------------------
$app->map = $app->protect(function($filter, $value) use ($app) {
    if (is_array($value) || $value instanceof \Traversable) {
        $results = array();
        foreach ($value as $name => $val) {
            $results[$name] = $app->map($filter, $val);
        }
        return $results;
    }
    return $filter($value);
});

//-----------------------------------------------------------------------------
// アプリケーションへのリクエストハンドラ登録
//-----------------------------------------------------------------------------
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

//-----------------------------------------------------------------------------
// アプリケーション初期処理
//-----------------------------------------------------------------------------
$app->addHandler('init', function(Application $app) {
    // $_SERVER
    $app->renderer->assign('server', $app->request->server->all());
    // セッション開始
    $app->session->start();
    // フラッシュメッセージ
    $app->renderer->assign('flash', $app->flash);
});

//-----------------------------------------------------------------------------
// アプリケーション実行
//-----------------------------------------------------------------------------
$app->run = $app->protect(function() use ($app) {

    $app->init();

    try {
        $method = $app->request->getMethod();
        $handlerName = 'on' . ucfirst(strtolower($method));
        if (!$app->offsetExists($handlerName)) {
            throw new HttpException(405);
        }
        $response = $app->{$handlerName}($app, $method);
    } catch (\Exception $e) {
        $app->logException($e);
        $response = $app->error($e);
    }

    // CSRFトークン自動出力
    ini_set('url_rewriter.tags', 'form=');
    output_add_rewrite_var(
        $app->escape($app->token->name()),
        $app->escape($app->token->value())
    );

    $response->send();
});

return $app;
