<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests;

use Acme\Form\Element;

/**
 * Test for Element
 *
 * @author k.holy74@gmail.com
 */
class ElementTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructor()
	{
		$element = new Element('myElement');
		$this->assertEquals('myElement', $element->getName());
	}

	public function testSetValue()
	{
		$element = new Element('myElement');
		$element->value('foo');
		$this->assertEquals('foo', $element->value());
	}

	public function testHasValue()
	{
		$element = new Element('myElement');
		$this->assertFalse($element->hasValue());
		$element->value('foo');
		$this->assertTrue($element->hasValue());
	}

	public function testSetError()
	{
		$element = new Element('myElement');
		$element->error('myElement is error');
		$this->assertEquals('myElement is error', $element->error());
	}

	public function testHasError()
	{
		$element = new Element('myElement');
		$this->assertFalse($element->hasError());
		$element->error('myElement is error');
		$this->assertTrue($element->hasError());
	}

	public function testContainsValueInArray()
	{
		$element = new Element('myElement');
		$this->assertFalse($element->contains('foo'));
		$this->assertFalse($element->contains('bar'));
		$element->value(array('foo'));
		$this->assertTrue($element->contains('foo'));
		$this->assertFalse($element->contains('bar'));
	}

	public function testContainsValueInTraversable()
	{
		$element = new Element('myElement');
		$this->assertFalse($element->contains('foo'));
		$this->assertFalse($element->contains('bar'));
		$element->value(new \ArrayIterator(array('foo')));
		$this->assertTrue($element->contains('foo'));
		$this->assertFalse($element->contains('bar'));
	}

	public function testEquals()
	{
		$element = new Element('myElement');
		$element->value('1');
		$this->assertTrue($element->equals('1'));
		$this->assertFalse($element->equals(1));
	}

	public function testIsEmptyNull()
	{
		$element = new Element('myElement');
		$element->value(null);
		$this->assertTrue($element->isEmpty());
	}

	public function testIsEmptyString()
	{
		$element = new Element('myElement');
		$element->value('');
		$this->assertTrue($element->isEmpty());
	}

	public function testIsEmptyArray()
	{
		$element = new Element('myElement');
		$element->value(array());
		$this->assertTrue($element->isEmpty());
	}

	public function testIsEmptyCountable()
	{
		$element = new Element('myElement');
		$element->value(new \ArrayIterator(array()));
		$this->assertTrue($element->isEmpty());
	}

	public function testIsNotEmptyBool()
	{
		$element = new Element('myElement');
		$element->value(true);
		$this->assertFalse($element->isEmpty());
		$element->value(false);
		$this->assertFalse($element->isEmpty());
	}

	public function testIsNotEmptyDateTime()
	{
		$element = new Element('myElement');
		$element->value(new \DateTime('0000-00-00 00:00:00'));
		$this->assertFalse($element->isEmpty());
	}

	public function testIsNotEmptyArrayOfEmptyString()
	{
		$element = new Element('myElement');
		$element->value(array('', '', ''));
		$this->assertFalse($element->isEmpty());
	}

	public function testIsNotEmptyArrayOfNull()
	{
		$element = new Element('myElement');
		$element->value(array(null, null, null));
		$this->assertFalse($element->isEmpty());
	}

	public function testArrayAccess()
	{
		$element = new Element('myElement');
		$element->value(array(
			'foo' => null,
			'bar' => null,
		));
		$this->assertNull($element['foo']);
		$this->assertNull($element['bar']);
		$element['foo'] = true;
		$element['bar'] = false;
		$this->assertTrue($element['foo']);
		$this->assertFalse($element['bar']);
	}

	public function testPropertyAccess()
	{
		$element = new Element('myElement');
		$element->value(array(
			'foo' => null,
			'bar' => null,
		));
		$this->assertNull($element->foo);
		$this->assertNull($element->bar);
		$element->foo = true;
		$element->bar = false;
		$this->assertTrue($element->foo);
		$this->assertFalse($element->bar);
	}

	public function testArrayAccessForObject()
	{
		$object = new \StdClass();
		$object->foo = null;
		$object->bar = null;
		$element = new Element('myElement');
		$element->value($object);
		$this->assertNull($element['foo']);
		$this->assertNull($element['bar']);
		$element['foo'] = true;
		$element['bar'] = false;
		$this->assertTrue($element['foo']);
		$this->assertFalse($element['bar']);
	}

	public function testPropertyAccessForObject()
	{
		$object = new \StdClass();
		$object->foo = null;
		$object->bar = null;
		$element = new Element('myElement');
		$element->value($object);
		$this->assertNull($element->foo);
		$this->assertNull($element->bar);
		$element->foo = true;
		$element->bar = false;
		$this->assertTrue($element->foo);
		$this->assertFalse($element->bar);
	}

	public function testArrayAccessForObjectMethod()
	{
		$object = new \ArrayObject(array(
			'foo' => null,
			'bar' => null,
		));
		$element = new Element('myElement');
		$element->value($object);
		$this->assertInstanceOf('\Countable', $object);
		$this->assertEquals(2, $element['count']);
	}

	public function testPropertyAccessForObjectMethod()
	{
		$object = new \ArrayObject(array(
			'foo' => null,
			'bar' => null,
		));
		$element = new Element('myElement');
		$element->value($object);
		$this->assertInstanceOf('\Countable', $object);
		$this->assertEquals(2, $element->count);
	}

	public function testArrayAccessForArrayAccess()
	{
		$object = new \ArrayObject(array(
			'foo' => null,
			'bar' => null,
		));
		$element = new Element('myElement');
		$element->value($object);
		$this->assertNull($element['foo']);
		$this->assertNull($element['bar']);
		$element['foo'] = true;
		$element['bar'] = false;
		$this->assertTrue($element['foo']);
		$this->assertFalse($element['bar']);
	}

	public function testPropertyAccessForArrayAccess()
	{
		$object = new \ArrayObject(array(
			'foo' => null,
			'bar' => null,
		));
		$element = new Element('myElement');
		$element->value($object);
		$this->assertNull($element->foo);
		$this->assertNull($element->bar);
		$element->foo = true;
		$element->bar = false;
		$this->assertTrue($element->foo);
		$this->assertFalse($element->bar);
	}

	public function testIsset()
	{
		$element = new Element('myElement');
		$element->value(array(
			'foo' => true,
			'bar' => null,
		));
		$this->assertTrue(isset($element['foo']));
		$this->assertFalse(isset($element['bar']));
	}

	public function testIssetPropertyAccess()
	{
		$element = new Element('myElement');
		$element->value(array(
			'foo' => true,
			'bar' => null,
		));
		$this->assertTrue(isset($element->foo));
		$this->assertFalse(isset($element->bar));
	}

	public function testIssetForArrayAccess()
	{
		$object = new \ArrayObject(array(
			'foo' => true,
			'bar' => null,
		));
		$element = new Element('myElement');
		$element->value($object);
		$this->assertTrue(isset($element['foo']));
		$this->assertFalse(isset($element['bar']));
	}

	public function testIssetForObject()
	{
		$object = new \StdClass();
		$object->foo = true;
		$object->bar = null;
		$element = new Element('myElement');
		$element->value($object);
		$this->assertTrue(isset($element->foo));
		$this->assertFalse(isset($element->bar));
	}

	public function testIssetForObjectMethod()
	{
		$object = new \ArrayObject(array(
			'foo' => null,
			'bar' => null,
		));
		$element = new Element('myElement');
		$element->value($object);
		$this->assertInstanceOf('\Countable', $object);
		$this->assertTrue(isset($element->count));
	}

	public function testToArray()
	{
		$element = new Element('myElement', array(
			'foo' => true,
			'bar' => false,
		));
		$values = $element->toArray();
		$this->assertArrayHasKey('foo', $values);
		$this->assertArrayHasKey('bar', $values);
		$this->assertTrue($values['foo']);
		$this->assertFalse($values['bar']);
	}

	public function testToArrayTraversable()
	{
		$element = new Element('myElement', new \ArrayIterator(array(
			'foo' => true,
			'bar' => false,
		)));
		$values = $element->toArray();
		$this->assertArrayHasKey('foo', $values);
		$this->assertArrayHasKey('bar', $values);
		$this->assertTrue($values['foo']);
		$this->assertFalse($values['bar']);
	}

}
