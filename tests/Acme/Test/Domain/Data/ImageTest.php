<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Data;

use Acme\Domain\Data\Image;

/**
 * Test for Image
 *
 * @author k.holy74@gmail.com
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructWithAttributes()
	{
		$image = new Image([
			'id'           => '1',
			'file_name'    => 'foo',
			'file_size'    => 100,
			'encoded_data' => 'encoded-data',
			'mime_type'    => 'image/png',
		], [
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$this->assertEquals('1', $image->id);
		$this->assertEquals('foo', $image->file_name);
		$this->assertEquals(100, $image->file_size);
		$this->assertEquals('encoded-data', $image->encoded_data);
		$this->assertEquals('image/png', $image->mime_type);
	}

	public function testCreatedAt()
	{
		$createdAt = new \DateTime(sprintf('@%d', time()));
		$image = new Image([
			'created_at' => $createdAt,
		], [
			'datetimeFormat' => 'Y/n/j H:i:s',
			'timezone'       => new \DateTimeZone('Asia/Tokyo'),
		]);
		$this->assertInstanceOf('\Acme\DateTime', $image->created_at);
		$this->assertEquals(
			$createdAt->getTimestamp(),
			$image->created_at->getTimestamp()
		);
	}

	public function testGetDataUri()
	{
		$image = new Image([
			'encoded_data' => 'encoded-data',
			'mime_type'    => 'image/png',
		], [
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$this->assertEquals('data:image/png;base64,encoded-data', $image->data_uri);
	}

	public function testGetFormattedFileSize()
	{
		$image = new Image([], [
			'byteScale' => 0,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$image->file_size = 100;
		$this->assertEquals('100B', $image->formatted_file_size);
		$image->file_size = 1024;
		$this->assertEquals('1KB', $image->formatted_file_size);
		$image->file_size = 1024 * 2;
		$this->assertEquals('2KB', $image->formatted_file_size);
		$image->file_size = 1024 * 1024 * 2;
		$this->assertEquals('2MB', $image->formatted_file_size);
		$image->file_size = 1024 * 1024 * 1024 * 2;
		$this->assertEquals('2GB', $image->formatted_file_size);
	}

	public function testGetFormattedFileSizeByBcMath()
	{
		if (!extension_loaded('bcmath')) {
			$this->markTestSkipped('BC Math extension is not loaded.');
		}
		$image = new Image([], [
			'byteScale' => 0,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$image->file_size = bcmul(bcpow('1024', '3'), '2');
		$this->assertEquals('2GB', $image->formatted_file_size);
		$image->file_size = bcmul(bcpow('1024', '4'), '2');
		$this->assertEquals('2TB', $image->formatted_file_size);
		$image->file_size = bcmul(bcpow('1024', '5'), '2');
		$this->assertEquals('2PB', $image->formatted_file_size);
		$image->file_size = bcmul(bcpow('1024', '6'), '2');
		$this->assertEquals('2EB', $image->formatted_file_size);
		$image->file_size = bcmul(bcpow('1024', '7'), '2');
		$this->assertEquals('2ZB', $image->formatted_file_size);
		$image->file_size = bcmul(bcpow('1024', '8'), '2');
		$this->assertEquals('2YB', $image->formatted_file_size);
	}

	public function testGetFormattedFileSizeByGmp()
	{
		if (!extension_loaded('gmp')) {
			$this->markTestSkipped('GMP extension is not loaded.');
		}
		$image = new Image([], [
			'byteScale' => 0,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$image->file_size = gmp_strval(gmp_mul(gmp_pow('1024', '3'), '2'));
		$this->assertEquals('2GB', $image->formatted_file_size);
		$image->file_size = gmp_strval(gmp_mul(gmp_pow('1024', '4'), '2'));
		$this->assertEquals('2TB', $image->formatted_file_size);
		$image->file_size = gmp_strval(gmp_mul(gmp_pow('1024', '5'), '2'));
		$this->assertEquals('2PB', $image->formatted_file_size);
		$image->file_size = gmp_strval(gmp_mul(gmp_pow('1024', '6'), '2'));
		$this->assertEquals('2EB', $image->formatted_file_size);
		$image->file_size = gmp_strval(gmp_mul(gmp_pow('1024', '7'), '2'));
		$this->assertEquals('2ZB', $image->formatted_file_size);
		$image->file_size = gmp_strval(gmp_mul(gmp_pow('1024', '8'), '2'));
		$this->assertEquals('2YB', $image->formatted_file_size);
	}

	public function testGetFormattedFileSizeWithByteScale()
	{
		$image = new Image([], [
			'byteScale' => 1,
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$image->file_size = 100;
		$this->assertEquals('100.0B', $image->formatted_file_size);
		$image->file_size = 1024;
		$this->assertEquals('1.0KB', $image->formatted_file_size);
		$image->file_size = 1024 * 2;
		$this->assertEquals('2.0KB', $image->formatted_file_size);
		$image->file_size = 1024 * 1024 * 2;
		$this->assertEquals('2.0MB', $image->formatted_file_size);
		$image->file_size = 1024 * 1024 * 1024 * 2;
		$this->assertEquals('2.0GB', $image->formatted_file_size);
	}

	public function testGetFormattedFileSizeWithByteUnits()
	{
		$image = new Image([], [
			'byteScale' => 0,
			'byteUnits' => [' Bytes', ' KiloBytes', ' MegaBytes', ' GigaBytes', ' TeraBytes', ' PetaBytes', ' ExaBytes', ' ZettaBytes', ' YottaBytes'],
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$image->file_size = 100;
		$this->assertEquals('100 Bytes', $image->formatted_file_size);
		$image->file_size = 1024;
		$this->assertEquals('1 KiloBytes', $image->formatted_file_size);
		$image->file_size = 1024 * 2;
		$this->assertEquals('2 KiloBytes', $image->formatted_file_size);
		$image->file_size = 1024 * 1024 * 2;
		$this->assertEquals('2 MegaBytes', $image->formatted_file_size);
		$image->file_size = 1024 * 1024 * 1024 * 2;
		$this->assertEquals('2 GigaBytes', $image->formatted_file_size);
	}

}
