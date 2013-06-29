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

$app->onRequestBy('GET|POST', function($app, $method) {

	$errors = array();

    $form = array(
        'name'    => $app->findVar('P', 'name'),
        'comment' => $app->findVar('P', 'comment'),
    );

    if ($method === 'POST') {

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
        'form'   => $form,
        'errors' => $errors,
    ));
});

$app->run();
