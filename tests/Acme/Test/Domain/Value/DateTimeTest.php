<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Value;

use Acme\Domain\Value\DateTime;

/**
 * Test for DateTime
 *
 * @author k.holy74@gmail.com
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructorAcceptString()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals('2013-05-01 12:34:56', $datetime->format('Y-m-d H:i:s'));
	}

	public function testConstructorAcceptTimestamp()
	{
		$time = time();
		$datetime = new DateTime($time);
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testConstructorAcceptDigit()
	{
		$time = time();
		$datetime = new DateTime(sprintf('%d', $time));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testConstructorAcceptDateTimeObject()
	{
		$time = time();
		$datetime = new DateTime(new \DateTime(date('Y-m-d H:i:s', $time)));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testGetYear()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(2013, $datetime->getYear());
	}

	public function testGetMonth()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(5, $datetime->getMonth());
	}

	public function testGetDay()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(1, $datetime->getDay());
	}

	public function testGetHour()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(12, $datetime->getHour());
	}

	public function testGetMinute()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(34, $datetime->getMinute());
	}

	public function testGetSecond()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(56, $datetime->getSecond());
	}

	public function testGetTimestamp()
	{
		$time = time();
		$datetime = new DateTime(new \DateTime(date('Y-m-d H:i:s', $time)));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testGetLastDay()
	{
		$datetime = new DateTime('2013-05-01');
		$this->assertEquals(31, $datetime->getLastday());

		$datetime = new DateTime('2013-04-01');
		$this->assertEquals(30, $datetime->getLastday());

		$datetime = new DateTime('2013-02-01');
		$this->assertEquals(28, $datetime->getLastday());

		$datetime = new DateTime('2100-02-01');
		$this->assertEquals(28, $datetime->getLastday());
	}

	public function testGetLastDayOfLeapYear()
	{
		$datetime = new DateTime('2000-02-01');
		$this->assertEquals(29, $datetime->getLastday());

		$datetime = new DateTime('2012-02-01');
		$this->assertEquals(29, $datetime->getLastday());
	}

	public function testPropertyAccessGet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals($datetime->getYear(), $datetime->year);
		$this->assertEquals($datetime->getMonth(), $datetime->month);
		$this->assertEquals($datetime->getDay(), $datetime->day);
		$this->assertEquals($datetime->getHour(), $datetime->hour);
		$this->assertEquals($datetime->getMinute(), $datetime->minute);
		$this->assertEquals($datetime->getSecond(), $datetime->second);
		$this->assertEquals($datetime->getTimestamp(), $datetime->timestamp);
		$this->assertEquals($datetime->getLastday(), $datetime->lastday);
	}

	public function testArrayAccessGet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals($datetime->getYear(), $datetime['year']);
		$this->assertEquals($datetime->getMonth(), $datetime['month']);
		$this->assertEquals($datetime->getDay(), $datetime['day']);
		$this->assertEquals($datetime->getHour(), $datetime['hour']);
		$this->assertEquals($datetime->getMinute(), $datetime['minute']);
		$this->assertEquals($datetime->getSecond(), $datetime['second']);
		$this->assertEquals($datetime->getTimestamp(), $datetime['timestamp']);
		$this->assertEquals($datetime->getLastday(), $datetime['lastday']);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenPropertyAccessGetMethodIsNotDefined()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime->UNDEFINED_NAME;
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenArrayAccessGetMethodIsNotDefined()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime['UNDEFINED_NAME'];
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testRaiseExceptionWhenPropertyAccessSet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime->year = '2014';
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testRaiseExceptionWhenArrayAccessSet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime['year'] = '2014';
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testRaiseExceptionWhenArrayAccessUnset()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		unset($datetime['year']);
	}

	public function testToStringWithFormat()
	{
		$datetime = new DateTime('2013-05-01 12:34:56', array(
			'format'   => 'Y/n/j G:i',
		));
		$this->assertEquals('2013/5/1 12:34', $datetime->__toString());
	}

	public function testSetTimezone()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime = new DateTime($utc, array(
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$this->assertEquals(32400, $datetime->datetime->getOffset());
	}

	public function testSetTimezoneAcceptString()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime1 = new DateTime($utc, array(
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$datetime2 = new DateTime($utc, array(
			'timezone' => 'Asia/Tokyo',
		));
		$this->assertEquals($datetime1->datetime->getOffset(), $datetime2->datetime->getOffset());
	}

	public function testConstructorDefensiveCopy()
	{
		$now = new \DateTime();
		$timezone = new \DateTimeZone('Asia/Tokyo');
		$datetime = new DateTime($now, array(
			'format' => 'Y-m-d H:i:s',
			'timezone' => $timezone,
		));
		$this->assertEquals($now, $datetime->value);
		$this->assertNotSame($now, $datetime->value);
		$this->assertEquals($timezone, $datetime->timezone);
		$this->assertNotSame($timezone, $datetime->timezone);
	}

	public function testToArray()
	{
		$datetime = new DateTime('2013-05-01 12:34:56', array(
			'format'   => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$array = $datetime->toArray();
		$this->assertEquals($datetime->value, $array['value']);
		$this->assertSame($datetime->value, $array['value']);
		$this->assertEquals($datetime->timezone, $array['timezone']);
		$this->assertSame($datetime->timezone, $array['timezone']);
	}

	public function testSerialize()
	{
		$datetime = new DateTime('2013-05-01 12:34:56', array(
			'format'   => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$deserialized = unserialize(serialize($datetime));
		$this->assertEquals($datetime, $deserialized);
		$this->assertNotSame($datetime, $deserialized);
		$this->assertEquals($datetime->value, $deserialized->value);
		$this->assertNotSame($datetime->value, $deserialized->value);
		$this->assertEquals($datetime->timezone, $deserialized->timezone);
		$this->assertNotSame($datetime->timezone, $deserialized->timezone);
	}

	public function testVarExport()
	{
		$datetime = new DateTime('2013-05-01 12:34:56', array(
			'format'   => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		eval('$exported = ' . var_export($datetime, true) . ';');
		$this->assertEquals($datetime, $exported);
		$this->assertNotSame($datetime, $exported);
		$this->assertEquals($datetime->value, $exported->value);
		$this->assertNotSame($datetime->value, $exported->value);
		$this->assertEquals($datetime->timezone, $exported->timezone);
		$this->assertNotSame($datetime->timezone, $exported->timezone);
	}

	public function testClone()
	{
		$datetime = new DateTime('2013-05-01 12:34:56', array(
			'format'   => \DateTime::RFC3339,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$cloned = clone $datetime;
		$this->assertEquals($datetime, $cloned);
		$this->assertNotSame($datetime, $cloned);
		$this->assertEquals($datetime->value, $cloned->value);
		$this->assertNotSame($datetime->value, $cloned->value);
		$this->assertEquals($datetime->timezone, $cloned->timezone);
		$this->assertNotSame($datetime->timezone, $cloned->timezone);
	}

	public function testGetValue()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime = new DateTime($utc, array(
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$this->assertEquals($utc, $datetime->getValue());
		$this->assertNotSame($utc, $datetime->getValue());
	}

}
