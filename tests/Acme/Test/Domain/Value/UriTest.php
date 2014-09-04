<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Value;

use Acme\Domain\Value\Uri;

/**
 * Test for Uri
 *
 * @author k.holy74@gmail.com
 */
class UriTest extends \PHPUnit_Framework_TestCase
{

	public function testSetPropertiesByUri()
	{
		$uri = new Uri('http://www.example.com/foo/bar/baz?param=value#fragment');
		$this->assertEquals('http', $uri->scheme);
		$this->assertEquals('www.example.com', $uri->host);
		$this->assertEquals('/foo/bar/baz', $uri->path);
		$this->assertEquals('param=value', $uri->query);
		$this->assertEquals('fragment', $uri->fragment);
	}

	public function testToString()
	{
		$uri = new Uri(null, array(
			'scheme' => 'http',
			'host' => 'www.example.com',
			'path' => '/foo/bar/baz.ext',
			'query' => 'param=value',
			'fragment' => 'fragment',
		));
		$this->assertEquals('http://www.example.com/foo/bar/baz.ext?param=value#fragment', $uri->__toString());
		$this->assertEquals('http://www.example.com/foo/bar/baz.ext?param=value#fragment', (string)$uri);
	}

	public function testGetExtension()
	{
		$uri = new Uri('/foo.bar.baz.ext');
		$this->assertEquals('ext', $uri->getExtension());
		$this->assertEquals('ext', $uri->extension);
	}

	public function testGetValue()
	{
		$uri = new Uri('http://www.example.com/foo/bar/baz?param=value#fragment');
		$this->assertEquals('http://www.example.com/foo/bar/baz?param=value#fragment', $uri->getValue());

		$uri = new Uri(null, array(
			'scheme' => 'http',
			'host' => 'www.example.com',
			'path' => '/foo/bar/baz.ext',
			'query' => 'param=value',
			'fragment' => 'fragment',
		));
		$this->assertEquals('http://www.example.com/foo/bar/baz.ext?param=value#fragment', $uri->getValue());
	}

	public function testIsHttps()
	{
		$uri = new Uri('http://www.example.com/foo/bar/baz?param=value#fragment');
		$this->assertFalse($uri->isHttps());
		$uri = new Uri('https://www.example.com/foo/bar/baz?param=value#fragment');
		$this->assertTrue($uri->isHttps());
	}

}
