<?php
/**
 * Create my own framework on top of the Pimple
 *
 * 投稿フォーム
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

$app->on('GET|POST', function($app, $method) {

    $errors = array();

    $form = array(
        'name'    => $app->findVar('P', 'name'),
        'comment' => $app->findVar('P', 'comment'),
    );

    if ($method === 'POST') {

        // CSRFトークンの検証
        if (!$app->csrfVerify('P')) {
            $app->abort(403, 'リクエストは無効です。');
        }

        // 投稿フォーム処理
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
            $app->flash->addSuccess('投稿を受け付けました');
            return $app->redirect('/');
        }

    }

    return $app->render('comment.html', array(
        'title'  => '投稿フォーム',
        'form'   => $form,
        'errors' => $errors,
    ));
});

$app->run();
