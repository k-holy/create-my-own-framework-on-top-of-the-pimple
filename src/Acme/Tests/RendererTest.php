<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests;

use Acme\Renderer;

/**
 * RendererTest
 *
 * @author k.holy74@gmail.com
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{

	private $template_dir;

	public function setUp()
	{
		$this->template_dir = __DIR__ . DIRECTORY_SEPARATOR . 'temp';
	}

	public function tearDown()
	{
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->template_dir)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
	}

	public function testConfig()
	{
		$renderer = new Renderer();
		$renderer->config('template_dir', $this->template_dir);
		$this->assertEquals($this->template_dir, $renderer->config('template_dir'));
	}

	public function testRender()
	{
		$renderer = new Renderer(array(
			'template_dir' => $this->template_dir,
		));

		$template = '/render.php';

		file_put_contents($this->template_dir . $template,
<<<'TEMPLATE'
<html>
<head>
<title><?=$title?></title>
</head>
<body>
</body>
</html>
TEMPLATE
		);

		$xml = simplexml_load_string($renderer->render($template, array('title' => 'TITLE')));
		$titles = $xml->xpath('/html/head/title');
		$title = (string)$titles[0];

		$this->assertEquals('TITLE', $title);
	}

	public function testAssignAndRender()
	{
		$renderer = new Renderer(array(
			'template_dir' => $this->template_dir,
		));

		$template = '/assign-and-render.php';

		file_put_contents($this->template_dir . $template,
<<<'TEMPLATE'
<html>
<head>
<title><?=$prefix?>-<?=$title?>-<?=$suffix?></title>
</head>
<body>
</body>
</html>
TEMPLATE
		);

		$renderer->assign('prefix', 'PREFIX');
		$renderer->assign('suffix', 'SUFFIX');

		$xml = simplexml_load_string($renderer->render($template, array('title' => 'TITLE')));
		$titles = $xml->xpath('/html/head/title');
		$title = (string)$titles[0];

		$this->assertEquals('PREFIX-TITLE-SUFFIX', $title);
	}

}
