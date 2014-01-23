<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Data\Test;

use Acme\Domain\Data\DateTime;

/**
 * Test for DateTime
 *
 * @author k.holy74@gmail.com
 */
class DateTimeTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructorAcceptString()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals('2013-05-01 12:34:56', $datetime->format('Y-m-d H:i:s'));
	}

	public function testConstructorAcceptTimestamp()
	{
		$time = time();
		$datetime = new DateTime(array(
			'datetime' => $time,
		));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testConstructorAcceptDigit()
	{
		$time = time();
		$datetime = new DateTime(array(
			'datetime' => sprintf('%d', $time),
		));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testConstructorAcceptDateTimeObject()
	{
		$time = time();
		$datetime = new DateTime(array(
			'datetime' => new \DateTime(date('Y-m-d H:i:s', $time)),
		));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testGetYear()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals(2013, $datetime->getYear());
	}

	public function testGetMonth()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals(5, $datetime->getMonth());
	}

	public function testGetDay()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals(1, $datetime->getDay());
	}

	public function testGetHour()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals(12, $datetime->getHour());
	}

	public function testGetMinute()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals(34, $datetime->getMinute());
	}

	public function testGetSecond()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertEquals(56, $datetime->getSecond());
	}

	public function testGetTimestamp()
	{
		$time = time();
		$datetime = new DateTime(array(
			'datetime' => new \DateTime(date('Y-m-d H:i:s', $time)),
		));
		$this->assertEquals($time, $datetime->getTimestamp());
	}

	public function testGetLastDay()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01',
		));
		$this->assertEquals(31, $datetime->getLastday());

		$datetime = new DateTime(array(
			'datetime' => '2013-04-01',
		));
		$this->assertEquals(30, $datetime->getLastday());

		$datetime = new DateTime(array(
			'datetime' => '2013-02-01',
		));
		$this->assertEquals(28, $datetime->getLastday());

		$datetime = new DateTime(array(
			'datetime' => '2100-02-01',
		));
		$this->assertEquals(28, $datetime->getLastday());
	}

	public function testGetLastDayOfLeapYear()
	{
		$datetime = new DateTime(array(
			'datetime' => '2000-02-01',
		));
		$this->assertEquals(29, $datetime->getLastday());

		$datetime = new DateTime(array(
			'datetime' => '2012-02-01',
		));
		$this->assertEquals(29, $datetime->getLastday());
	}

	public function testPropertyAccessGet()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
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
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
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
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$datetime->UNDEFINED_NAME;
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenArrayAccessGetMethodIsNotDefined()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$datetime['UNDEFINED_NAME'];
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testRaiseExceptionWhenPropertyAccessSet()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$datetime->year = '2014';
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testRaiseExceptionWhenArrayAccessSet()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$datetime['year'] = '2014';
	}

	/**
	 * @expectedException \LogicException
	 */
	public function testRaiseExceptionWhenArrayAccessUnset()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		unset($datetime['year']);
	}

	public function testSetFormatAndToString()
	{
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
			'format'   => 'Y/n/j G:i',
		));
		$this->assertEquals('2013/5/1 12:34', $datetime->__toString());
	}

	public function testSetTimezone()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime = new DateTime(array(
			'datetime' => $utc,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$this->assertEquals(32400, $datetime->datetime->getOffset());
	}

	public function testSetTimezoneAcceptString()
	{
		$utc = new \DateTime(sprintf('@%d', time()));
		$datetime1 = new DateTime(array(
			'datetime' => $utc,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		));
		$datetime2 = new DateTime(array(
			'datetime' => $utc,
			'timezone' => 'Asia/Tokyo',
		));
		$this->assertEquals($datetime1->datetime->getOffset(), $datetime2->datetime->getOffset());
	}

/* TODO
	public function testImplementsDateTimeInterface()
	{
		if (version_compare(PHP_VERSION, '5.5.0', '<')) {
			$this->markTestSkipped('Test skipped, for PHP 5.5 or higher.');
		}
		$datetime = new DateTime(array(
			'datetime' => '2013-05-01 12:34:56',
		));
		$this->assertInstanceOf('\\DateTimeInterface', $datetime);
	}
*/

}
