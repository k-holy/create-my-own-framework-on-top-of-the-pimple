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

$app->on('GET', function($app) {

    $statement = $app->db->prepare("SELECT author, comment, posted_at FROM comments LIMIT :limit OFFSET :offset");
    $statement->execute(['limit' => 20, 'offset' => 0]);
    $comments = $statement->fetchAll(function($author, $comment, $posted_at) use ($app) {
        $object = $app->createData('Comment', [
            'timezone' => $app->config->timezone,
        ]);
        $object->author = $author;
        $object->comment = $comment;
        $object->posted_at = $posted_at;
        return $object;
    });

    return $app->render('index.html', [
        'title'    => 'トップページ',
        'comments' => $comments,
    ]);

});

$app->run();
