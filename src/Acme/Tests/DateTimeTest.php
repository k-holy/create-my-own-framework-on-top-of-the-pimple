<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests;

use Acme\DateTime;

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
		$this->assertEquals($time, $datetime->timestamp());
	}

	public function testConstructorAcceptDigit()
	{
		$time = time();
		$datetime = new DateTime(sprintf('%d', $time));
		$this->assertEquals($time, $datetime->timestamp());
	}

	public function testConstructorAcceptDateTimeObject()
	{
		$time = time();
		$datetime = new DateTime(new \DateTime(date('Y-m-d H:i:s', $time)));
		$this->assertEquals($time, $datetime->timestamp());
	}

	public function testGetYear()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(2013, $datetime->year());
	}

	public function testGetMonth()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(5, $datetime->month());
	}

	public function testGetDay()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(1, $datetime->day());
	}

	public function testGetHour()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(12, $datetime->hour());
	}

	public function testGetMinute()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(34, $datetime->minute());
	}

	public function testGetSecond()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(56, $datetime->second());
	}

	public function testGetTimestamp()
	{
		$time = time();
		$datetime = new DateTime(date('Y-m-d H:i:s', $time));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testGetLastDay()
	{
		$datetime = new DateTime('2013-05-01');
		$this->assertEquals(31, $datetime->lastDay());

		$datetime = new DateTime('2013-04-01');
		$this->assertEquals(30, $datetime->lastDay());

		$datetime = new DateTime('2013-02-01');
		$this->assertEquals(28, $datetime->lastDay());

		$datetime = new DateTime('2100-02-01');
		$this->assertEquals(28, $datetime->lastDay());
	}

	public function testGetLastDayOfLeapYear()
	{
		$datetime = new DateTime('2000-02-01');
		$this->assertEquals(29, $datetime->lastDay());

		$datetime = new DateTime('2012-02-01');
		$this->assertEquals(29, $datetime->lastDay());
	}

	public function testDiffTime()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(1, $datetime->diffTime('2013-05-01 12:34:57'));

		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(1, $datetime->diffTime('2013-05-01 12:34:55'));
	}

	public function testDiffTimeInvert()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(1, $datetime->diffTime('2013-05-01 12:34:57', true));

		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals(-1, $datetime->diffTime('2013-05-01 12:34:55', true));
	}

	public function testPropertyAccessGet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals($datetime->year(), $datetime->year);
		$this->assertEquals($datetime->month(), $datetime->month);
		$this->assertEquals($datetime->day(), $datetime->day);
		$this->assertEquals($datetime->hour(), $datetime->hour);
		$this->assertEquals($datetime->minute(), $datetime->minute);
		$this->assertEquals($datetime->second(), $datetime->second);
		$this->assertEquals($datetime->timestamp(), $datetime->timestamp);
		$this->assertEquals($datetime->lastDay(), $datetime->lastDay);
	}

	public function testArrayAccessGet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertEquals($datetime->year(), $datetime['year']);
		$this->assertEquals($datetime->month(), $datetime['month']);
		$this->assertEquals($datetime->day(), $datetime['day']);
		$this->assertEquals($datetime->hour(), $datetime['hour']);
		$this->assertEquals($datetime->minute(), $datetime['minute']);
		$this->assertEquals($datetime->second(), $datetime['second']);
		$this->assertEquals($datetime->timestamp(), $datetime['timestamp']);
		$this->assertEquals($datetime->lastDay(), $datetime['lastDay']);
	}

	public function testArrayAccessIsset()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertTrue(isset($datetime['year']));
		$this->assertFalse(isset($datetime['UNDEFINED_NAME']));
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testRaiseExceptionWhenPropertyAccessGetMethodIsNotDefined()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime->UNDEFINED_NAME;
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testRaiseExceptionWhenArrayAccessGetMethodIsNotDefined()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime['UNDEFINED_NAME'];
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testRaiseExceptionWhenPropertyAccessSet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime->year = '2014';
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testRaiseExceptionWhenArrayAccessSet()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime['year'] = '2014';
	}

	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testRaiseExceptionWhenArrayAccessUnset()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		unset($datetime['year']);
	}

	public function testSetFormatAndToString()
	{
		$datetime = new DateTime('2013-05-01 12:34:56');
		$datetime->setFormat('Y/n/j G:i');
		$this->assertEquals('2013/5/1 12:34', $datetime->__toString());
	}

	public function testSetTimeZone()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime = new DateTime($utc);
		$this->assertEquals(0, $datetime->getOffset());
		$datetime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
		$this->assertEquals(32400, $datetime->getOffset());
	}

	public function testSetTimeZoneAcceptString()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime1 = new DateTime($utc);
		$datetime1->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
		$datetime2 = new DateTime($utc);
		$datetime2->setTimeZone('Asia/Tokyo');
		$this->assertEquals($datetime1->getOffset(), $datetime2->getOffset());
	}

/* TODO
	public function testImplementsDateTimeInterface()
	{
		if (version_compare(PHP_VERSION, '5.5.0', '<')) {
			$this->markTestSkipped('Test skipped, for PHP 5.5 or higher.');
		}
		$datetime = new DateTime('2013-05-01 12:34:56');
		$this->assertInstanceOf('\\DateTimeInterface', $datetime);
	}
*/

}
