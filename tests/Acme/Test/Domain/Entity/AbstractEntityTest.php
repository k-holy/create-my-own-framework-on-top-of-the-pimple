<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Entity;

/**
 * Test for AbstractEntity
 *
 * @author k.holy74@gmail.com
 */
class AbstractEntityTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructorDefensiveCopy()
	{
		$now = new \DateTime();
		$test = new AbstractEntityTestData(array(
			'datetime' => $now,
		));
		$this->assertEquals($now, $test->datetime);
		$this->assertNotSame($now, $test->datetime);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorRaiseInvalidArgumentExceptionUndefinedProperty()
	{
		$test = new AbstractEntityTestData(array(
			'undefined_property' => 'Foo',
		));
	}

	public function testIsset()
	{
		$test = new AbstractEntityTestData(array(
			'string' => 'Foo',
			'null'   => null,
		));
		$this->assertTrue(isset($test->string));
		$this->assertFalse(isset($test->null));
		$this->assertFalse(isset($test->undefined_property));
	}

	public function testGet()
	{
		$test = new AbstractEntityTestData(array(
			'string' => 'Foo',
			'null'   => null,
		));
		$this->assertEquals('Foo', $test->string);
		$this->assertNull($test->null);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetRaiseInvalidArgumentExceptionUndefinedProperty()
	{
		$test = new AbstractEntityTestData();
		$test->undefined_property;
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testSetRaiseLogicException()
	{
		$test = new AbstractEntityTestData(array(
			'string'  => 'Foo',
			'boolean' => true,
		));
		$test->string = 'Bar';
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testUnsetRaiseLogicException()
	{
		$test = new AbstractEntityTestData(array(
			'string' => 'Foo',
		));
		unset($test->string);
	}

	public function testGetDatetimeAsString()
	{
		$now = new \DateTime();
		$test = new AbstractEntityTestData(array(
			'datetime' => $now,
		));
		$this->assertEquals($now->format('Y-m-d H:i:s'), $test->datetimeAsString);
	}

	public function testGetDatetimeAsStringWithDateFormat()
	{
		$now = new \DateTime();
		$test = new AbstractEntityTestData(array(
			'datetime'   => $now,
			'dateFormat' => \DateTime::RFC3339,
		));
		$this->assertEquals($now->format(\DateTime::RFC3339), $test->datetimeAsString);
	}

	public function testSerialize()
	{
		$test = new AbstractEntityTestData(array(
			'string'     => 'Foo',
			'null'       => null,
			'boolean'    => true,
			'datetime'   => new \DateTime(),
			'dateFormat' => \DateTime::RFC3339,
		));
		$deserialized = unserialize(serialize($test));
		$this->assertEquals($test, $deserialized);
		$this->assertNotSame($test, $deserialized);
		$this->assertEquals($test->datetime, $deserialized->datetime);
		$this->assertNotSame($test->datetime, $deserialized->datetime);
	}

	public function testVarExport()
	{
		$test = new AbstractEntityTestData(array(
			'string'     => 'Foo',
			'null'       => null,
			'boolean'    => true,
			'datetime'   => new \DateTime(),
			'dateFormat' => \DateTime::RFC3339,
		));
		eval('$exported = ' . var_export($test, true) . ';');
		$this->assertEquals($test, $exported);
		$this->assertNotSame($test, $exported);
		$this->assertEquals($test->datetime, $exported->datetime);
		$this->assertNotSame($test->datetime, $exported->datetime);
	}

	public function testClone()
	{
		$test = new AbstractEntityTestData(array(
			'string'     => 'Foo',
			'null'       => null,
			'boolean'    => true,
			'datetime'   => new \DateTime(),
			'dateFormat' => \DateTime::RFC3339,
		));
		$cloned = clone $test;
		$this->assertEquals($test, $cloned);
		$this->assertNotSame($test, $cloned);
		$this->assertEquals($test->datetime, $cloned->datetime);
		$this->assertNotSame($test->datetime, $cloned->datetime);
	}

	public function testIteration()
	{
		$now = new \DateTime();
		$properties = array(
			'string'     => 'Foo',
			'null'       => null,
			'boolean'    => true,
			'datetime'   => $now,
			'dateFormat' => \DateTime::RFC3339,
		);
		$test = new AbstractEntityTestData($properties);
		foreach ($test as $name => $value) {
			if (array_key_exists($name, $properties)) {
				switch ($name) {
				case 'datetime':
					$this->assertEquals($now, $value);
					$this->assertNotSame($now, $value);
					$this->assertEquals($now->format(\DateTime::RFC3339), $value->format(\DateTime::RFC3339));
					break;
				default:
					$this->assertEquals($properties[$name], $value);
					break;
				}
			}
		}
	}

}
