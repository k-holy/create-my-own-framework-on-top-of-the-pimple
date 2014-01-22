<?php
/**
 * Create my own framework on top of the Pimple
 *
 * フレームワーク機能テスト
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

$app->on('GET|POST', function($app, $method) {

    $errors = array();

    $form = array(
        'enable_debug'      => $app->findVar('P', 'enable_debug'),
        'move_log_dir'      => $app->findVar('P', 'move_log_dir'),
        'ignore_error'      => $app->findVar('P', 'ignore_error'),
        'change_secret_key' => $app->findVar('P', 'change_secret_key'),
        'validate_token'    => $app->findVar('P', 'validate_token'),
    );

    if ($method === 'POST') {

        // デバッグ設定を動的に切り替える
        $app->config->debug = (isset($form['enable_debug']));

        if (isset($form['move_log_dir'])) {
            $app->config->log_dir = $app->config->web_root;
        }

        // error_reporting設定を変更
        if (isset($form['ignore_error'])) {
            switch ($form['ignore_error']) {
            // Error以下を無視
            case 'error':
                $error_reporting = error_reporting();
                if ($error_reporting & E_ERROR) {
                    $error_reporting = error_reporting($error_reporting &~E_ERROR);
                }
                if ($error_reporting & E_USER_ERROR) {
                    $error_reporting = error_reporting($error_reporting &~E_USER_ERROR);
                }
            // Warning以下を無視
            case 'warning':
                $error_reporting = error_reporting();
                if ($error_reporting & E_WARNING) {
                    $error_reporting = error_reporting($error_reporting &~E_WARNING);
                }
                if ($error_reporting & E_USER_WARNING) {
                    $error_reporting = error_reporting($error_reporting &~E_USER_WARNING);
                }
            // Notice以下を無視
            case 'notice':
                $error_reporting = error_reporting();
                if ($error_reporting & E_NOTICE) {
                    $error_reporting = error_reporting($error_reporting &~E_NOTICE);
                }
                if ($error_reporting & E_USER_NOTICE) {
                    $error_reporting = error_reporting($error_reporting &~E_USER_NOTICE);
                }
            // Info以下を無視
            case 'info':
                $error_reporting = error_reporting();
                if ($error_reporting & E_STRICT) {
                    $error_reporting = error_reporting($error_reporting &~E_STRICT);
                }
                if ($error_reporting & E_DEPRECATED) {
                    $error_reporting = error_reporting($error_reporting &~E_DEPRECATED);
                }
                if ($error_reporting & E_USER_DEPRECATED) {
                    $error_reporting = error_reporting($error_reporting &~E_USER_DEPRECATED);
                }
                break;
            }
        }

        // 秘密のキーを変更
        if (isset($form['change_secret_key'])) {
            $app->config->secret_key = bin2hex(openssl_random_pseudo_bytes(32));
        }

        // CSRFトークンの検証
        if (isset($form['validate_token']) && !$app->csrfVerify('P')) {
            $app->abort(403, 'リクエストは無効です。');
        }

        // PHPエラーのテスト
        if (!is_null($app->findVar('P', 'trigger-info'))) {
            trigger_error('[E_USER_DEPRECATED]PHPエラーのテストです', E_USER_DEPRECATED);
        }

        if (!is_null($app->findVar('P', 'trigger-notice'))) {
            trigger_error('[E_USER_NOTICE]PHPエラーのテストです', E_USER_NOTICE);
        }

        if (!is_null($app->findVar('P', 'trigger-warning'))) {
            trigger_error('[E_USER_WARNING]PHPエラーのテストです', E_USER_WARNING);
        }

        if (!is_null($app->findVar('P', 'trigger-error'))) {
            trigger_error('[E_USER_ERROR]PHPエラーのテストです', E_USER_ERROR);
        }

        // HTTP例外のテスト
        if (!is_null($app->findVar('P', 'throw-http-exception-400'))) {
            $app->abort(400, 'HttpException[400]のテストです');
        }

        if (!is_null($app->findVar('P', 'throw-http-exception-403'))) {
            $app->abort(403,'HttpException[403]のテストです');
        }

        if (!is_null($app->findVar('P', 'throw-http-exception-404'))) {
            $app->abort(404, 'HttpException[404]のテストです');
        }

        if (!is_null($app->findVar('P', 'throw-http-exception-405'))) {
            $app->abort(405, 'HttpException[405]のテストです');
        }

        // 組み込み例外のテスト
        if (!is_null($app->findVar('P', 'throw-runtime-exception'))) {
            throw new RuntimeException('RuntimeExceptionのテストです');
        }

        // フラッシュメッセージのテスト
        if (!is_null($app->findVar('P', 'flash-error'))) {
            $app->flash->addError('フラッシュメッセージ[Error]のテストです');
            return $app->redirect($app->requestUri());
        }

        if (!is_null($app->findVar('P', 'flash-alert'))) {
            $app->flash->addAlert('フラッシュメッセージ[Alert]のテストです');
            return $app->redirect($app->requestUri());
        }

        if (!is_null($app->findVar('P', 'flash-success'))) {
            $app->flash->addSuccess('フラッシュメッセージ[Success]のテストです');
            return $app->redirect($app->requestUri());
        }

        if (!is_null($app->findVar('P', 'flash-info'))) {
            $app->flash->addInfo('フラッシュメッセージ[Info]のテストです');
            return $app->redirect($app->requestUri());
        }

    }

    return $app->render('test.html', array(
        'title'  => 'フレームワーク機能テスト',
        'form'   => $form,
        'errors' => $errors,
    ));
});

$app->run();
