<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests\Renderer;

use Acme\Renderer\PhpTalRenderer;

/**
 * PhpTalRendererTest
 *
 * @author k.holy74@gmail.com
 */
class PhpTalRendererTest extends \PHPUnit_Framework_TestCase
{

	private $templateRepository;
	private $phpCodeDestination;

	public function setUp()
	{
		$this->templateRepository = __DIR__ . DIRECTORY_SEPARATOR . 'temp';
		$this->phpCodeDestination  = __DIR__ . DIRECTORY_SEPARATOR . 'temp_c';
	}

	public function tearDown()
	{
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->templateRepository)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->phpCodeDestination)
		);
		foreach ($it as $file) {
			if ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
	}

	public function testConfig()
	{
		$renderer = new PhpTalRenderer();
		$renderer->config('outputMode', \PHPTAL::XHTML);
		$renderer->config('encoding', 'UTF-8');
		$renderer->config('templateRepository', $this->templateRepository);
		$renderer->config('phpCodeDestination', $this->phpCodeDestination);
		$renderer->config('phpCodeExtension', 'php');
		$renderer->config('cacheLifetime', 0);
		$renderer->config('forceReparse', true);
		$this->assertEquals(\PHPTAL::XHTML, $renderer->config('outputMode'));
		$this->assertEquals('UTF-8', $renderer->config('encoding'));
		$this->assertEquals($this->templateRepository, $renderer->config('templateRepository'));
		$this->assertEquals($this->phpCodeDestination, $renderer->config('phpCodeDestination'));
		$this->assertEquals('php', $renderer->config('phpCodeExtension'));
		$this->assertEquals(0, $renderer->config('cacheLifetime'));
		$this->assertTrue($renderer->config('forceReparse'));
	}

	public function testFetch()
	{
		$renderer = new PhpTalRenderer(array(
			'templateRepository' => $this->templateRepository,
			'phpCodeDestination' => $this->phpCodeDestination,
			'cacheLifetime'      => 0,
			'forceReparse'       => true,
		));

		$template = '/render.html';

		file_put_contents($this->templateRepository . $template,
<<<'TEMPLATE'
<html>
<head>
<title tal:content="title">Title is here.</title>
</head>
<body>
</body>
</html>
TEMPLATE
		);

		$xml = simplexml_load_string($renderer->fetch($template, array('title' => 'TITLE')));
		$titles = $xml->xpath('/html/head/title');
		$title = (string)$titles[0];

		$this->assertEquals('TITLE', $title);
	}

	public function testAssignAndFetch()
	{
		$renderer = new PhpTalRenderer(array(
			'templateRepository' => $this->templateRepository,
			'phpCodeDestination' => $this->phpCodeDestination,
			'cacheLifetime'      => 0,
			'forceReparse'       => true,
		));

		$renderer->assign('prefix', 'PREFIX');
		$renderer->assign('suffix', 'SUFFIX');

		$template = '/assign-and-render.html';

		file_put_contents($this->templateRepository . $template,
<<<'TEMPLATE'
<html>
<head>
<title><tal:block tal:content="prefix">Prefix is here.</tal:block>-<tal:block tal:content="title">Title is here.</tal:block>-<tal:block tal:content="suffix">Suffix is here.</tal:block></title>
</head>
<body>
</body>
</html>
TEMPLATE
		);

		$xml = simplexml_load_string($renderer->fetch($template, array('title' => 'TITLE')));
		$titles = $xml->xpath('/html/head/title');
		$title = (string)$titles[0];

		$this->assertEquals('PREFIX-TITLE-SUFFIX', $title);
	}

}
