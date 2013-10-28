<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests\Domain\Data;

use Acme\Domain\Data\DataTrait;

/**
 * Test for DataTrait
 *
 * @author k.holy74@gmail.com
 */
class DataTraitTest extends \PHPUnit_Framework_TestCase
{

	public function testPropertyAccess()
	{
		$test = new Test();
		$test->id   = '1';
		$test->name = 'foo';
		$this->assertEquals('foo', $test->name);
		$this->assertEquals('1', $test->id);
	}

	public function testArrayAccess()
	{
		$test = new Test();
		$test['id'] = '1';
		$test['name'] = 'foo';
		$this->assertEquals('1', $test['id']);
		$this->assertEquals('foo', $test['name']);
	}

	public function testIsset()
	{
		$test = new Test();
		$test['id'] = '1';
		$test['name'] = null;
		$this->assertTrue(isset($test['id']));
		$this->assertTrue(isset($test['name'])); // Attention!!
	}

	public function testNotIsset()
	{
		$test = new Test();
		$test['name'] = null;
		$this->assertTrue(isset($test['name'])); // Attention!!
		$this->assertFalse(isset($test['undefined_property']));
	}

	public function testEmpty()
	{
		$test = new Test();
		$test['id'] = '0';
		$test['name'] = null;
		$this->assertTrue(empty($test['id'])); // Attention!!
		$this->assertTrue(empty($test['name']));
	}

	public function testNotEmpty()
	{
		$test = new Test();
		$test['id'] = '1';
		$test['name'] = 'foo';
		$this->assertFalse(empty($test['id']));
		$this->assertFalse(empty($test['name']));
	}

	public function testMagicPropertyUnderscore()
	{
		$test = new Test();
		$test->parent_name = 'foo';
		$this->assertEquals('foo', $test->parent_name);
	}

	public function testMagicPropertyCamelCase()
	{
		$test = new Test();
		$test->parentName = 'foo';
		$this->assertEquals('foo', $test->parentName);
	}

	public function testPseudoGetterUnderscore()
	{
		$test = new Test();
		$test->name = 'foo';
		$this->assertEquals('FOO', $test->upper_name);
	}

	public function testPseudoGetterCamelCase()
	{
		$test = new Test();
		$test->name = 'foo';
		$this->assertEquals('FOO', $test->upperName);
	}

	public function testMagicPropertyUseTimezone()
	{
		$test = new Test(array(), new \DateTimeZone('Asia/Tokyo'));
		$test->created_at = '2013-09-25T05:50:29+00:00';
		$this->assertEquals('2013-09-25 14:50:29', $test->created_at->format('Y-m-d H:i:s'));
	}

	public function testTraversableReturnOriginalValue()
	{
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$createdAt = new \DateTime('2013-09-25 15:00', $timezone);
		$test = new Test(array(), $timezone);
		$test->id = '1';
		$test->name = 'foo';
		$test->parent_name = 'bar';
		$test->created_at = $createdAt;
		foreach ($test as $name => $value) {
			switch ($name) {
			case 'id':
				$this->assertEquals('1', $value);
				break;
			case 'name':
				$this->assertEquals('foo', $value);
				break;
			case 'parent_name':
				$this->assertEquals('bar', $value);
				break;
			case 'created_at':
				$this->assertNotInstanceOf('\DateTime', $value);
				$this->assertEquals($createdAt->getTimestamp(), $value);
				break;
			}
		}
	}

	public function testToArrayReturnOffsetGet()
	{
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$createdAt = new \DateTime('2013-09-25 15:00', $timezone);
		$test = new Test(array(), $timezone);
		$test->id = '1';
		$test->name = 'foo';
		$test->parent_name = 'bar';
		$test->created_at = $createdAt;
		foreach ($test->toArray() as $name => $value) {
			switch ($name) {
			case 'id':
				$this->assertEquals('1', $value);
				break;
			case 'name':
				$this->assertEquals('foo', $value);
				break;
			case 'parent_name':
				$this->assertEquals('bar', $value);
				break;
			case 'created_at':
				$this->assertInstanceOf('\DateTime', $value);
				$this->assertEquals($createdAt, $value);
				break;
			}
		}
	}

	public function testToStringExportArray()
	{
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$createdAt = new \DateTime('2013-09-25 15:00', $timezone);
		$test = new Test(array(), $timezone);
		$test->id = '1';
		$test->name = 'foo';
		$test->parent_name = 'bar';
		$test->created_at = $createdAt;
		$export = (string)$test;
		eval('$test2 = ' . $export . ';');
		$this->assertEquals($test2['id'         ], $test->id);
		$this->assertEquals($test2['name'       ], $test->name);
		$this->assertEquals($test2['parent_name'], $test->parent_name);
		$this->assertEquals($test2['created_at' ], $test->created_at);
	}

}

class Test implements \ArrayAccess, \IteratorAggregate
{
	use DataTrait;

	private $timezone;
	private $attributes = [
		'id'          => null,
		'name'        => null,
		'parent_name' => null,
		'created_at'  => null,
	];

	public function __construct($attributes = array(), \DateTimeZone $timezone = null)
	{
		if (isset($timezone)) {
			$this->timezone = $timezone;
		}
		$this->setAttributes($attributes);
	}

	public function set_parent_name($value)
	{
		$this->attributes['parent_name'] = $value;
		return $this;
	}

	public function setParentName($value)
	{
		$this->set_parent_name($value);
		return $this;
	}

	public function get_parent_name()
	{
		if (isset($this->attributes['parent_name'])) {
			return $this->attributes['parent_name'];
		}
		return null;
	}

	public function getParentName()
	{
		return $this->get_parent_name();
	}

	public function get_upper_name()
	{
		if (isset($this->attributes['name'])) {
			return strtoupper($this->attributes['name']);
		}
		return null;
	}

	public function getUpperName()
	{
		return $this->get_upper_name();
	}

	public function set_created_at($datetime)
	{
		// StringまたはDateTimeを引数に取り、内部的には値をUnix Timestampで保持
		if (false === $datetime instanceof \DateTime) {
			$datetime = new \DateTime($datetime);
		}
		$datetime->setTimezone($this->timezone);
		$this->attributes['created_at'] = $datetime->getTimestamp();
	}

	public function get_created_at()
	{
		// Unix Timestamp形式の場合Timezoneを持っていないので、セットして返してやる
		if (isset($this->attributes['created_at'])) {
			$datetime = new \DateTime(sprintf('@%d', $this->attributes['created_at']));
			$datetime->setTimezone($this->timezone);
			return $datetime;
		}
		return null;
	}

}
