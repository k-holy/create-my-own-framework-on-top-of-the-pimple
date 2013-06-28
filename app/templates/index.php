<html>
<body>
<h1>俺のフレームワーク@<?=$app->escape($server['HTTP_HOST'])?></h1>

<form method="post" action="<?=$app->escape($server['REQUEST_URI'])?>">

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
