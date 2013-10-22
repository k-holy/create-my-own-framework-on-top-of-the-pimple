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
        'author'  => $app->findVar('P', 'author'),
        'comment' => $app->findVar('P', 'comment'),
    );

    if ($method === 'POST') {

        // CSRFトークンの検証
        if (!$app->csrfVerify('P')) {
            $app->abort(403, 'リクエストは無効です。');
        }

        // 投稿フォーム処理
        if (strlen($form['author']) === 0) {
            $errors['author'] = '名前を入力してください。';
        } elseif (mb_strlen($form['author']) > 20) {
            $errors['author'] = '名前は20文字以内で入力してください。';
        }

        if (strlen($form['comment']) === 0) {
            $errors['comment'] = 'コメントを入力してください。';
        } elseif (mb_strlen($form['comment']) > 50) {
            $errors['comment'] = 'コメントは50文字以内で入力してください。';
        }

        if (empty($errors)) {

            $comment = $app->createData('comment', [
                'author'    => $form['author'],
                'comment'   => $form['comment'],
                'posted_at' => $app->clock->format('Y-m-d H:i:s'),
            ], [
                'timezone' => $app->timezone,
            ]);

            $statement = $app->db->prepare(<<<'SQL'
INSERT INTO comments (
    author
   ,comment
   ,posted_at
) VALUES (
    :author
   ,:comment
   ,:posted_at
)
SQL
            );

            $app->transaction->begin();

            try {
                $statement->execute($comment);
                $app->transaction->commit();
            } catch (\Exception $e) {
                $app->transaction->rollback();
                throw $e;
            }

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
