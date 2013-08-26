<?php
/**
 * Create my own framework on top of the Pimple
 *
 * データベース
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
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
    return $app->redirect('/database', 303);

});

$app->run();
