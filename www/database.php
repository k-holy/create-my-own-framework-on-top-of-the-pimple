<?php
/**
 * Create my own framework on top of the Pimple
 *
 * データベース
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

$app->on('GET', function($app) {

    $tables = array();
    foreach ($app->db->getMetaTables() as $table) {
        $tables[$table['name']] = $app->db->getMetaColumns($table['name']);
    }

    return $app->render('database.html', array(
        'title'  => 'データベース',
        'tables' => $tables,
    ));

});

$app->on('POST', function($app) {

    $app->db->execute('DROP TABLE IF EXISTS images;');
    $app->db->execute(<<<'SQL'
CREATE TABLE images
(
     id           INTEGER      NOT NULL PRIMARY KEY
    ,file_name    VARCHAR(255) NOT NULL
    ,file_size    INTEGER      NOT NULL
    ,encoded_data TEXT
    ,mime_type    VARCHAR(64)  NOT NULL
    ,width        INTEGER      NOT NULL
    ,height       INTEGER      NOT NULL
    ,created_at   INTEGER      NOT NULL
);
SQL
    );

    $app->db->execute('DROP TABLE IF EXISTS comments;');
    $app->db->execute(<<<'SQL'
CREATE TABLE comments
(
     id        INTEGER      NOT NULL PRIMARY KEY
    ,author    VARCHAR(255) NOT NULL
    ,comment   TEXT         NOT NULL
    ,posted_at INTEGER      NOT NULL
);
SQL
    );

    $app->db->execute('DROP TABLE IF EXISTS comment_images;');
    $app->db->execute(<<<'SQL'
CREATE TABLE comment_images
(
     comment_id INTEGER     NOT NULL
    ,image_id   INTEGER     NOT NULL
    ,FOREIGN KEY(comment_id) REFERENCES comments(id) ON DELETE CASCADE
    ,FOREIGN KEY(image_id) REFERENCES images(id) ON DELETE CASCADE
);
SQL
    );

    $app->pdo->sqliteCreateFunction('regexp', function($pattern, $value) {
        mb_regex_encoding('UTF-8');
        return (false !== mb_ereg($pattern, $value)) ? 1 : 0;
    });

    $app->metaCache->unsetMetaTables();
    $app->metaCache->unsetMetaColumns('images');
    $app->metaCache->unsetMetaColumns('comments');

    return $app->redirect('/database', 303);

});

$app->run();
