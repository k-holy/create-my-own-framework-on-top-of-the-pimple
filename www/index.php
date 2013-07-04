<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Step 6
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include realpath(__DIR__ . '/../app/app.php');

$app->on('GET|POST', function($app, $method) {

    $errors = array();

    $form = array(
        'name'         => $app->findVar('P', 'name'),
        'comment'      => $app->findVar('P', 'comment'),
        'enable_debug' => $app->findVar('P', 'enable_debug'),
        'move_log_dir' => $app->findVar('P', 'move_log_dir'),
    );

    // 設定を動的に切り替える
    $app->config->debug = (isset($form['enable_debug']));

    if (isset($form['move_log_dir'])) {
        $app->config->log_dir = $app->config->web_root;
    }

    if ($method === 'POST') {

        if (!is_null($app->findVar('P', 'trigger-notice'))) {
            trigger_error('[E_USER_NOTICE]PHPエラーのテストです', E_USER_NOTICE);
        }

        if (!is_null($app->findVar('P', 'trigger-warning'))) {
            trigger_error('[E_USER_WARNING]PHPエラーのテストです', E_USER_WARNING);
        }

        if (!is_null($app->findVar('P', 'trigger-error'))) {
            trigger_error('[E_USER_ERROR]PHPエラーのテストです', E_USER_ERROR);
        }

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

        if (!is_null($app->findVar('P', 'throw-runtime-exception'))) {
            throw new RuntimeException('RuntimeExceptionのテストです');
        }

        if (strlen($form['name']) === 0) {
            $errors['name'] = '名前を入力してください。';
        } elseif (mb_strlen($form['name']) > 20) {
            $errors['name'] = '名前は20文字以内で入力してください。';
        }

        if (strlen($form['comment']) === 0) {
            $errors['comment'] = 'コメントを入力してください。';
        } elseif (mb_strlen($form['comment']) > 50) {
            $errors['comment'] = 'コメントは50文字以内で入力してください。';
        }

        if (empty($errors)) {
            // $app->addFlashMessage('success', '投稿を受け付けました');
            return $app->redirect('/');
        }

    }

    return $app->render('index.html', array(
        'title'  => '投稿フォーム',
        'form'   => $form,
        'errors' => $errors,
    ));
});

$app->run();
