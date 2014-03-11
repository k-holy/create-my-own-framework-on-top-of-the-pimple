<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Value;

use Acme\Value\Byte;

/**
 * Test for Byte
 *
 * @author k.holy74@gmail.com
 */
class ByteTest extends \PHPUnit_Framework_TestCase
{

	private function mulAndPow($num, $pow)
	{
		return gmp_strval(gmp_mul(sprintf('%d', $num), gmp_pow('1024', $pow)));
	}

	private function getKiloByte($num)
	{
		return $this->mulAndPow($num, '1');
	}

	private function getMegaByte($num)
	{
		return $this->mulAndPow($num, '2');
	}

	private function getGigaByte($num)
	{
		return $this->mulAndPow($num, '3');
	}

	private function getTeraByte($num)
	{
		return $this->mulAndPow($num, '4');
	}

	private function getPetaByte($num)
	{
		return $this->mulAndPow($num, '5');
	}

	private function getExaByte($num)
	{
		return $this->mulAndPow($num, '6');
	}

	private function getZettaByte($num)
	{
		return $this->mulAndPow($num, '7');
	}

	private function getYottaByte($num)
	{
		return $this->mulAndPow($num, '8');
	}

	public function testConstructorWithByte()
	{
		$byte = new Byte('1B');
		$this->assertEquals('1', $byte->value);

		$byte = new Byte('2B');
		$this->assertEquals('2', $byte->value);
	}

	public function testConstructorWithKiloByte()
	{
		$byte = new Byte('1KB');
		$this->assertEquals($this->getKiloByte(1), $byte->value);

		$byte = new Byte('2KB');
		$this->assertEquals($this->getKiloByte(2), $byte->value);
	}

	public function testConstructorWithMegaByte()
	{
		$byte = new Byte('1MB');
		$this->assertEquals($this->getMegaByte(1), $byte->value);

		$byte = new Byte('2MB');
		$this->assertEquals($this->getMegaByte(2), $byte->value);
	}

	public function testConstructorWithGigaByte()
	{
		$byte = new Byte('1GB');
		$this->assertEquals($this->getGigaByte(1), $byte->value);

		$byte = new Byte('2GB');
		$this->assertEquals($this->getGigaByte(2), $byte->value);
	}

	public function testConstructorWithTeraByte()
	{
		$byte = new Byte('1TB');
		$this->assertEquals($this->getTeraByte(1), $byte->value);

		$byte = new Byte('2TB');
		$this->assertEquals($this->getTeraByte(2), $byte->value);
	}

	public function testConstructorWithPetaByte()
	{
		$byte = new Byte('1PB');
		$this->assertEquals($this->getPetaByte(1), $byte->value);

		$byte = new Byte('2PB');
		$this->assertEquals($this->getPetaByte(2), $byte->value);
	}

	public function testConstructorWithExaByte()
	{
		$byte = new Byte('1EB');
		$this->assertEquals($this->getExaByte(1), $byte->value);

		$byte = new Byte('2EB');
		$this->assertEquals($this->getExaByte(2), $byte->value);
	}

	public function testConstructorWithZettaByte()
	{
		$byte = new Byte('1ZB');
		$this->assertEquals($this->getZettaByte(1), $byte->value);

		$byte = new Byte('2ZB');
		$this->assertEquals($this->getZettaByte(2), $byte->value);
	}

	public function testConstructorWithYottaByte()
	{
		$byte = new Byte('1YB');
		$this->assertEquals($this->getYottaByte(1), $byte->value);

		$byte = new Byte('2YB');
		$this->assertEquals($this->getYottaByte(2), $byte->value);
	}

	public function testFormatByte()
	{
		$byte = new Byte('1', array(
			'decimals' => 0,
		));
		$this->assertEquals('1B', $byte->format());

		$byte = new Byte('2', array(
			'decimals' => 0,
		));
		$this->assertEquals('2B', $byte->format());
	}

	public function testFormatKiloByte()
	{
		$byte = new Byte($this->getKiloByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1KB', $byte->format());

		$byte = new Byte($this->getKiloByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2KB', $byte->format());
	}

	public function testFormatMegaByte()
	{
		$byte = new Byte($this->getMegaByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1MB', $byte->format());

		$byte = new Byte($this->getMegaByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2MB', $byte->format());
	}

	public function testFormatGigaByte()
	{
		$byte = new Byte($this->getGigaByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1GB', $byte->format());

		$byte = new Byte($this->getGigaByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2GB', $byte->format());
	}

	public function testFormatTeraByte()
	{
		$byte = new Byte($this->getTeraByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1TB', $byte->format());

		$byte = new Byte($this->getTeraByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2TB', $byte->format());
	}

	public function testFormatPetaByte()
	{
		$byte = new Byte($this->getPetaByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1PB', $byte->format());

		$byte = new Byte($this->getPetaByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2PB', $byte->format());
	}

	public function testFormatExaByte()
	{
		$byte = new Byte($this->getExaByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1EB', $byte->format());

		$byte = new Byte($this->getExaByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2EB', $byte->format());
	}

	public function testFormatZettaByte()
	{
		$byte = new Byte($this->getZettaByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1ZB', $byte->format());

		$byte = new Byte($this->getZettaByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2ZB', $byte->format());
	}

	public function testFormatYottaByte()
	{
		$byte = new Byte($this->getYottaByte(1), array(
			'decimals' => 0,
		));
		$this->assertEquals('1YB', $byte->format());

		$byte = new Byte($this->getYottaByte(2), array(
			'decimals' => 0,
		));
		$this->assertEquals('2YB', $byte->format());
	}

	public function testFormatByteWithDecimals()
	{
		$byte = new Byte(1024 + 512);
		$this->assertEquals('1.5KB', $byte->format(1));
		$this->assertEquals('1.50KB', $byte->format(2));

		$byte = new Byte(1024 + 512 + 256);
		$this->assertEquals('1.8KB', $byte->format(1));
		$this->assertEquals('1.75KB', $byte->format(2));
		$this->assertEquals('1.750KB', $byte->format(3));

		$byte = new Byte(1024 + 512 + 256 + 128);
		$this->assertEquals('1.9KB', $byte->format(1));
		$this->assertEquals('1.88KB', $byte->format(2));
		$this->assertEquals('1.875KB', $byte->format(3));
		$this->assertEquals('1.8750KB', $byte->format(4));

		$byte = new Byte(1024 + 512 + 256 + 128 + 64);
		$this->assertEquals('1.9KB', $byte->format(1));
		$this->assertEquals('1.94KB', $byte->format(2));
		$this->assertEquals('1.938KB', $byte->format(3));
		$this->assertEquals('1.9375KB', $byte->format(4));
		$this->assertEquals('1.93750KB', $byte->format(5));

		$byte = new Byte(1024 + 512 + 256 + 128 + 64 + 32);
		$this->assertEquals('2.0KB', $byte->format(1));
		$this->assertEquals('1.97KB', $byte->format(2));
		$this->assertEquals('1.969KB', $byte->format(3));
		$this->assertEquals('1.9688KB', $byte->format(4));
		$this->assertEquals('1.96875KB', $byte->format(5));
		$this->assertEquals('1.968750KB', $byte->format(6));

		$byte = new Byte(1024 + 512 + 256 + 128 + 64 + 32 + 16);
		$this->assertEquals('2.0KB', $byte->format(1));
		$this->assertEquals('1.98KB', $byte->format(2));
		$this->assertEquals('1.984KB', $byte->format(3));
		$this->assertEquals('1.9844KB', $byte->format(4));
		$this->assertEquals('1.98438KB', $byte->format(5));
		$this->assertEquals('1.984375KB', $byte->format(6));
		$this->assertEquals('1.9843750KB', $byte->format(7));

		$byte = new Byte(1024 + 512 + 256 + 128 + 64 + 32 + 16 + 8);
		$this->assertEquals('2.0KB', $byte->format(1));
		$this->assertEquals('1.99KB', $byte->format(2));
		$this->assertEquals('1.992KB', $byte->format(3));
		$this->assertEquals('1.9922KB', $byte->format(4));
		$this->assertEquals('1.99219KB', $byte->format(5));
		$this->assertEquals('1.992188KB', $byte->format(6));
		$this->assertEquals('1.9921875KB', $byte->format(7));
		$this->assertEquals('1.99218750KB', $byte->format(8));

		$byte = new Byte(1024 + 512 + 256 + 128 + 64 + 32 + 16 + 8 + 4);
		$this->assertEquals('2.0KB', $byte->format(1));
		$this->assertEquals('2.00KB', $byte->format(2));
		$this->assertEquals('1.996KB', $byte->format(3));
		$this->assertEquals('1.9961KB', $byte->format(4));
		$this->assertEquals('1.99609KB', $byte->format(5));
		$this->assertEquals('1.996094KB', $byte->format(6));
		$this->assertEquals('1.9960938KB', $byte->format(7));
		$this->assertEquals('1.99609375KB', $byte->format(8));
		$this->assertEquals('1.996093750KB', $byte->format(9));
	}

}
