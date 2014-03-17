<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Value;

/**
 * Test for AbstractValue
 *
 * @author k.holy74@gmail.com
 */
class AbstractValueTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructorDefensiveCopy()
	{
		$now = new \DateTime();
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$test = new AbstractValueTestData($now, array(
			'format' => 'Y-m-d H:i:s',
			'timezone' => $timezone,
		));
		$this->assertEquals($now, $test->value);
		$this->assertNotSame($now, $test->value);
		$this->assertEquals($timezone, $test->timezone);
		$this->assertNotSame($timezone, $test->timezone);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorRaiseInvalidArgumentExceptionUndefinedProperty()
	{
		$now = new \DateTime();
		$test = new AbstractValueTestData($now, array(
			'undefined_property' => 'Foo',
		));
	}

	public function testIsset()
	{
		$now = new \DateTime();
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$test = new AbstractValueTestData($now, array(
			'format' => 'Y-m-d H:i:s',
			'timezone' => $timezone,
		));
		$this->assertTrue(isset($test->format));
		$this->assertTrue(isset($test->timezone));
		$this->assertFalse(isset($test->undefined_property));
	}

	public function testGet()
	{
		$now = new \DateTime();
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$test = new AbstractValueTestData($now, array(
			'format' => 'Y-m-d H:i:s',
			'timezone' => $timezone,
		));
		$this->assertEquals('Y-m-d H:i:s', $test->format);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetRaiseInvalidArgumentExceptionUndefinedProperty()
	{
		$test = new AbstractValueTestData();
		$test->undefined_property;
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testSetRaiseLogicException()
	{
		$test = new AbstractValueTestData();
		$test->format = 'Y/m/d';
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testUnsetRaiseLogicException()
	{
		$test = new AbstractValueTestData();
		unset($test->format);
	}

	public function testToString()
	{
		$now = new \DateTime();
		$test = new AbstractValueTestData($now);
		$this->assertEquals($now->format('Y-m-d H:i:s'), (string)$test);
	}

	public function testToStringWithDateFormat()
	{
		$now = new \DateTime();
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$test = new AbstractValueTestData($now, array(
			'format' => \DateTime::RFC3339,
			'timezone' => $timezone,
		));
		$now->setTimezone($timezone);
		$this->assertEquals($now->format(\DateTime::RFC3339), (string)$test);
	}

	public function testSerialize()
	{
		$test = new AbstractValueTestData(new \DateTime(), array(
			'format' => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$deserialized = unserialize(serialize($test));
		$this->assertEquals($test, $deserialized);
		$this->assertNotSame($test, $deserialized);
		$this->assertEquals($test->value, $deserialized->value);
		$this->assertNotSame($test->value, $deserialized->value);
		$this->assertEquals($test->timezone, $deserialized->timezone);
		$this->assertNotSame($test->timezone, $deserialized->timezone);
	}

	public function testVarExport()
	{
		$test = new AbstractValueTestData(new \DateTime(), array(
			'format' => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		eval('$exported = ' . var_export($test, true) . ';');
		$this->assertEquals($test, $exported);
		$this->assertNotSame($test, $exported);
		$this->assertEquals($test->value, $exported->value);
		$this->assertNotSame($test->value, $exported->value);
		$this->assertEquals($test->timezone, $exported->timezone);
		$this->assertNotSame($test->timezone, $exported->timezone);
	}

	public function testClone()
	{
		$test = new AbstractValueTestData(new \DateTime(), array(
			'format' => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$cloned = clone $test;
		$this->assertEquals($test, $cloned);
		$this->assertNotSame($test, $cloned);
		$this->assertEquals($test->value, $cloned->value);
		$this->assertNotSame($test->value, $cloned->value);
		$this->assertEquals($test->timezone, $cloned->timezone);
		$this->assertNotSame($test->timezone, $cloned->timezone);
	}

	public function testIteration()
	{
		$now = new \DateTime();
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$properties = array(
			'format' => \DateTime::RFC3339,
			'timezone' => $timezone,
		);
		$test = new AbstractValueTestData($now, $properties);
		foreach ($test as $name => $value) {
			if (array_key_exists($name, $properties)) {
				switch ($name) {
				case 'value':
					$this->assertEquals($now, $value);
					$this->assertNotSame($now, $value);
					$this->assertEquals($now->format(\DateTime::RFC3339), $value->format(\DateTime::RFC3339));
					break;
				case 'timezone':
					$this->assertEquals($timezone, $value);
					$this->assertNotSame($timezone, $value);
					break;
				case 'format':
					$this->assertEquals($properties[$name], $value);
					break;
				}
			}
		}
	}

}
