<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test;

use Acme\DataObject;

/**
 * Test for DataObject
 *
 * @author k.holy74@gmail.com
 */
class DataObjectTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructorAcceptArray()
	{
		$data = new DataObject(array(
			'foo' => true,
			'bar' => false,
		));
		$this->assertTrue($data->foo);
		$this->assertFalse($data->bar);
	}

	public function testConstructorAcceptTraversable()
	{
		$data = new DataObject(new \ArrayIterator(array(
			'foo' => true,
			'bar' => false,
		)));
		$this->assertTrue($data->foo);
		$this->assertFalse($data->bar);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionInvalidArgument()
	{
		$data = new DataObject('foo');
	}

	public function testSetAndGetAnAttributeByArrayAccess()
	{
		$data = new DataObject();
		$data['foo'] = true;
		$data['bar'] = false;
		$this->assertTrue($data['foo']);
		$this->assertFalse($data['bar']);
	}

	public function testSetAndGetAnAttributeByPropertyAccess()
	{
		$data = new DataObject();
		$data->foo = true;
		$data->bar = false;
		$this->assertTrue($data->foo);
		$this->assertFalse($data->bar);
	}

	public function testTraversable()
	{
		$data = new DataObject();
		$data->foo = true;
		$data->bar = false;
		$this->assertInstanceOf('\Traversable', $data);
		foreach ($data as $name => $value) {
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

	public function testCallCallableAttribute()
	{
		$data = new DataObject();
		$data->callable = function($value) {
			return $value;
		};
		$this->assertEquals('Foo', $data->callable('Foo'));
	}

	public function testToArraySortedByAttributeName()
	{
		$data = new DataObject(array(
			'charlie' => null,
			'alfa'    => null,
			'delta'   => null,
			'bravo'   => null,
		));
		$this->assertEquals(
			array('alfa', 'bravo', 'charlie', 'delta'),
			array_keys($data->toArray())
		);
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testRaiseExceptionWhenUndefinedMethodCalled()
	{
		$data = new DataObject();
		$data->foo();
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenAttributeIsAlreadyDefinedAsAMethod()
	{
		$data = new DataObject(array(
			'offsetExists' => true,
		));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenPropertyIsAlreadyDefinedAsAMethod()
	{
		$data = new DataObject();
		$data->getIterator = function() {
			return 'Foo';
		};
	}

}
