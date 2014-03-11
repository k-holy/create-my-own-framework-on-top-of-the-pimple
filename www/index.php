<?php
/**
 * Create my own framework on top of the Pimple
 *
 * トップページ
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
, img.id AS "image:id"
, img.file_name AS "image:file_name"
, img.file_size AS "image:file_size"
, img.encoded_data AS "image:encoded_data"
, img.mime_type AS "image:mime_type"
, img.width AS "image:width"
, img.height AS "image:height"
, img.created_at AS "image:created_at"
FROM
  comments com
LEFT OUTER JOIN
  images img
ON com.image_id = img.id
LIMIT :limit OFFSET :offset
SQL
    );

    $statement->execute(['limit' => 20, 'offset' => 0]);

    $statement->setFetchMode(Statement::FETCH_ASSOC);
    $statement->setFetchCallback(function($cols) use ($app) {

        // 画像
        if (!is_null($cols['image:id'])) {
            $image = $app->createEntity('image', [
                'id' => (int)$cols['image:id'],
                'fileName' => $cols['image:file_name'],
                'fileSize' => $app->createValue('byte', $cols['image:file_size']),
                'encodedData' => $cols['image:encoded_data'],
                'mimeType' => $cols['image:mime_type'],
                'width' => (int)$cols['image:width'],
                'height' => (int)$cols['image:height'],
                'createdAt' => $app->createValue('dateTime', $cols['image:created_at']),
            ]);
        }

        // コメント
        $comment = $app->createEntity('comment', [
            'id' => (int)$cols['id'],
            'author' => $cols['author'],
            'comment' => $cols['comment'],
            'imageId' => (int)$cols['image_id'],
            'postedAt' => $app->createValue('dateTime',$cols['posted_at']),
            'image' => (isset($image)) ? $image : null,
        ]);

        return $comment;
    });

    return $app->render('index.html', [
        'title'    => 'トップページ',
        'comments' => $statement,
    ]);

});

$app->run();
