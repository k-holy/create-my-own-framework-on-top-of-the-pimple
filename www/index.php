<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Step 4
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include realpath(__DIR__ . '/../app/app.php');

$form = array(
    'name'    => $app->findVar('P', 'name'),
    'comment' => $app->findVar('P', 'comment'),
);
?>
<html>
<body>
<h1>test</h1>

<form method="post" action="<?=$app->escape($app->findVar('S', 'REQUEST_URI'))?>">

<dl>
<dt>名前</dt>
<dd>
<input type="text" name="name" value="<?=$app->escape($form['name'])?>" />
</dd>
<dt>コメント</dt>
<dd>
<textarea name="comment">
<?=$app->escape($form['comment'])?></textarea>
</dd>
</dl>

<input type="submit" value="送信" />
</form>

<hr />

<dl>
<dt>名前</dt>
<dd><?=$app->escape($form['name'])?></dd>
<dt>コメント</dt>
<dd><pre><?=$app->escape($form['comment'])?></pre></dd>
</dl>

</body>
</html>
