<?php
/**
 * Create my own framework on top of the Pimple
 *
 * トップページ
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Volcanus\Database\Statement;

$app->on('GET', function($app) {

    $statement = $app->db->prepare("SELECT author, comment, posted_at FROM comments LIMIT :limit OFFSET :offset");
    $statement->execute(['limit' => 20, 'offset' => 0]);
    $statement->setFetchMode(Statement::FETCH_FUNC, function($author, $comment, $posted_at) use ($app) {
        return $app->createData('comment', [
            'author'    => $author,
            'comment'   => $comment,
            'posted_at' => $posted_at,
        ]);
    });
    return $app->render('index.html', [
        'title'    => 'トップページ',
        'comments' => $statement,
    ]);

});

$app->run();
