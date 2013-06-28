<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Step 5
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include realpath(__DIR__ . '/../app/app.php');

$form = array(
    'name'    => $app->findVar('P', 'name'),
    'comment' => $app->findVar('P', 'comment'),
);

echo $app->renderer->render('index.php', array(
    'form' => $form,
));
