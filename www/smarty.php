<?php
/**
 * Create my own framework on top of the Pimple
 *
 * Volcanus_TemplateRenderer Smartyコンテンツ
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
$app = include __DIR__ . DIRECTORY_SEPARATOR . 'app.php';

use Volcanus\TemplateRenderer\Adapter\SmartyAdapter;

$app->on('GET', function($app, $method) {

    $app->renderer->assign('title', 'Volcanus_TemplateRenderer Smartyコンテンツ');

    $renderer = clone $app->renderer;

    $renderer->setAdapter(new SmartyAdapter(new \Smarty(), array(
        'template_dir'    => __DIR__,
        'compile_dir'     => sys_get_temp_dir(),
        'left_delimiter'  => '{{',
        'right_delimiter' => '}}',
        'caching'         => false,
        'force_compile'   => true,
        'use_sub_dirs'    => false,
        'escape_html'     => true,
    )));

    $contents = $renderer->fetch(
        sprintf('string:%s', <<<'HTML'
<hr />
<h2>{{$title}}</h2>
<div class="row">
{{if isset($server)}}
    <table class="table table-striped table-condensed">
        <caption><h3>$_SERVER環境変数</h3></caption>
        <tbody>
{{foreach $server as $key => $var}}
            <tr>
                <th>{{$key}}</th>
                <td>{{$var}}</td>
            </tr>
{{/foreach}}
        </tbody>
    </table>
{{/if}}
</div>
<hr />
HTML
        )
    );

    return $app->render('smarty.html', array(
        'contents' => $contents,
    ));

});

$app->run();
