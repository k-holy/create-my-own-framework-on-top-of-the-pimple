<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests;

use Acme\Configuration;

/**
 * Test for Configuration
 *
 * @author k.holy74@gmail.com
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

	private $config;

	public function testConstructorAcceptArray()
	{
		$config = new Configuration(array(
			'foo' => true,
			'bar' => false,
		));
		$this->assertTrue($config->foo);
		$this->assertFalse($config->bar);
	}

	public function testConstructorAcceptTraversable()
	{
		$config = new Configuration(new \ArrayIterator(array(
			'foo' => true,
			'bar' => false,
		)));
		$this->assertTrue($config->foo);
		$this->assertFalse($config->bar);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionInvalidArgument()
	{
		$config = new Configuration('foo');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionInvalidArgumentCount()
	{
		$config = new Configuration('foo', 'bar', 'baz');
	}

	public function testSetAndGetAnAttributeByArrayAccess()
	{
		$config = new Configuration(array(
			'foo' => true,
			'bar' => false,
		));
		$this->assertTrue($config['foo']);
		$this->assertFalse($config['bar']);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenGetNotDefinedAttribute()
	{
		$config = new Configuration();
		$config->bar;
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenSetNotDefinedAttribute()
	{
		$config = new Configuration();
		$config->bar = 'A';
	}

	public function testTraversable()
	{
		$config = new Configuration(array(
			'foo' => true,
			'bar' => false,
		));
		$this->assertInstanceOf('\Traversable', $config);
		foreach ($config as $name => $value) {
			switch ($name) {
			case 'foo':
				$this->assertTrue($value);
				break;
			case 'bar':
				$this->assertFalse($value);
				break;
			}
		}
	}

	public function testGetCallableAttribute()
	{
		$config = new Configuration(array(
			'callable' => function() {
				return 'Foo';
			},
		));
		$this->assertEquals('Foo', $config->callable);
	}

	public function testCallCallableAttribute()
	{
		$config = new Configuration(array(
			'callable' => function($value) {
				return $value;
			},
		));
		$this->assertEquals('Foo', $config->callable('Foo'));
	}

	public function testRecursiveArrayAccess()
	{
		$config = new Configuration(array(
			'array' => array('a' => 'A', 'b' => 'B', 'c' => 'C'),
			'object' => new \ArrayObject(array(
				'a' => new \ArrayObject(array(
					'a' => array('a' => 'A', 'b' => 'B', 'c' => array('a' => 'A', 'b' => 'B', 'c'=> 'C')),
				)),
			)),
		));
		$this->assertEquals('A', $config['array']['a']);
		$this->assertEquals('B', $config['array']['b']);
		$this->assertEquals('C', $config['array']['c']);
		$this->assertEquals('A', $config['object']['a']['a']['a']);
		$this->assertEquals('B', $config['object']['a']['a']['b']);
		$this->assertEquals('A', $config['object']['a']['a']['c']['a']);
		$this->assertEquals('B', $config['object']['a']['a']['c']['b']);
		$this->assertEquals('C', $config['object']['a']['a']['c']['c']);
	}

	public function testRecursiveObjectAccess()
	{
		$config = new Configuration(array(
			'array' => array('a' => 'A', 'b' => 'B', 'c' => 'C'),
			'object' => new \ArrayObject(array(
				'a' => new \ArrayObject(array(
					'a' => array('a' => 'A', 'b' => 'B', 'c' => array('a' => 'A', 'b' => 'B', 'c'=> 'C')),
				)),
			)),
		));
		$this->assertEquals('A', $config->array->a);
		$this->assertEquals('B', $config->array->b);
		$this->assertEquals('C', $config->array->c);
		$this->assertEquals('A', $config->object->a->a->a);
		$this->assertEquals('B', $config->object->a->a->b);
		$this->assertEquals('A', $config->object->a->a->c->a);
		$this->assertEquals('B', $config->object->a->a->c->b);
		$this->assertEquals('C', $config->object->a->a->c->c);
	}
}