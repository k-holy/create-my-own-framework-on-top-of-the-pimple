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

    $form = $app->createForm([
        'author'  => $app->findVar('P', 'author'),
        'comment' => $app->findVar('P', 'comment'),
    ]);

    if ($method === 'POST') {

        // CSRFトークンの検証
        if (!$app->csrfVerify('P')) {
            $app->abort(403, 'リクエストは無効です。');
        }

        // 投稿フォーム処理
        if (strlen($form->author->value) === 0) {
            $form->author->error = '名前を入力してください。';
        } elseif (mb_strlen($form->author->value) > 20) {
            $form->author->error = '名前は20文字以内で入力してください。';
        }

        if (strlen($form->comment->value) === 0) {
            $form->comment->error = 'コメントを入力してください。';
        } elseif (mb_strlen($form->comment->value) > 50) {
            $form->comment->error = 'コメントは50文字以内で入力してください。';
        }

        if (!$form->hasError()) {

            $comment = $app->createData('comment', [
                'author'  => $form->author->value,
                'comment' => $form->comment->value,
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

            $cols = [];
            foreach ($comment as $name => $value) {
                $cols[] = sprintf('%s = %s', $name, $value);
            }

            $app->flash->addSuccess(sprintf('投稿を受け付けました (%s)', implode(', ', $cols)));
            return $app->redirect('/');
        }

    }

    return $app->render('comment.html', [
        'title'  => '投稿フォーム',
        'form'   => $form,
    ]);
});

$app->run();
