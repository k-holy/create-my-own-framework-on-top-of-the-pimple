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

    $statement = $app->db->prepare(<<<'SQL'
SELECT
  com.id
, com.author
, com.comment
, com.image_id
, com.posted_at
, img.file_name AS "image_file_name"
, img.file_size AS "image_file_size"
, img.file_path AS "image_file_path"
, img.encoded_data AS "image_encoded_data"
, img.mime_type AS "image_mime_type"
, img.width AS "image_width"
, img.height AS "image_height"
, img.created_at AS "image_created_at"
FROM
  comments com
LEFT OUTER JOIN
  images img
ON com.image_id = img.id
LIMIT :limit OFFSET :offset
SQL
    );

    $statement->execute(['limit' => 20, 'offset' => 0]);

    $statement->setFetchMode(Statement::FETCH_FUNC, function(
        $id,
        $author,
        $comment,
        $image_id,
        $posted_at,
        $image_file_name,
        $image_file_size,
        $image_file_path,
        $image_encoded_data,
        $image_mime_type,
        $image_width,
        $image_height,
        $image_created_at
    ) use ($app) {
        $comment = $app->createData('comment', [
            'id'        => $id,
            'author'    => $author,
            'comment'   => $comment,
            'image_id'  => $image_id,
            'posted_at' => $posted_at,
        ]);
        if (!is_null($image_id)) {
            $image = $app->createData('image', [
                'id'           => $image_id,
                'file_name'    => $image_file_name,
                'file_size'    => $image_file_size,
                'file_path'    => $image_file_path,
                'encoded_data' => $image_encoded_data,
                'mime_type'    => $image_mime_type,
                'width'        => $image_width,
                'height'       => $image_height,
                'created_at'   => $image_created_at,
            ]);
            $comment->image = $image;
        }
        return $comment;
    });

    return $app->render('index.html', [
        'title'    => 'トップページ',
        'comments' => $statement,
    ]);

});

$app->run();
