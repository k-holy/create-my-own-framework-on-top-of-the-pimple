<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
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

	public function testConstructWithProperties()
	{
		$createdAt = $this->getMock('Acme\Domain\Data\DateTime');

		$image = new Image(array(
			'id'          => '1',
			'fileName'    => 'foo',
			'fileSize'    => 100,
			'encodedData' => 'encoded-data',
			'mimeType'    => 'image/png',
			'createdAt'   => $createdAt,
		));

		$this->assertEquals('1', $image->id);
		$this->assertEquals('foo', $image->fileName);
		$this->assertEquals(100, $image->fileSize);
		$this->assertEquals('encoded-data', $image->encodedData);
		$this->assertEquals('image/png', $image->mimeType);
		$this->assertInstanceOf('Acme\Domain\Data\DateTime', $image->createdAt);
	}

	public function testGetDataUri()
	{
		$image = new Image(array(
			'encodedData' => 'encoded-data',
			'mimeType'    => 'image/png',
		));

		$this->assertEquals('data:image/png;base64,encoded-data', $image->dataUri);
	}

	public function testGetFormattedFileSize()
	{
		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 100,
		));

		$this->assertEquals('100B', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024,
		));

		$this->assertEquals('1KB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024 * 2,
		));

		$this->assertEquals('2KB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024 * 1024 * 2,
		));

		$this->assertEquals('2MB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024 * 1024 * 1024 * 2,
		));

		$this->assertEquals('2GB', $image->formattedFileSize);
	}

	public function testGetFormattedFileSizeByBcMath()
	{
		if (!extension_loaded('bcmath')) {
			$this->markTestSkipped('BC Math extension is not loaded.');
		}

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => bcmul(bcpow('1024', '3'), '2'),
		));

		$this->assertEquals('2GB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => bcmul(bcpow('1024', '4'), '2'),
		));

		$this->assertEquals('2TB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => bcmul(bcpow('1024', '5'), '2'),
		));

		$this->assertEquals('2PB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => bcmul(bcpow('1024', '6'), '2'),
		));

		$this->assertEquals('2EB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => bcmul(bcpow('1024', '7'), '2'),
		));

		$this->assertEquals('2ZB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => bcmul(bcpow('1024', '8'), '2'),
		));

		$this->assertEquals('2YB', $image->formattedFileSize);
	}

	public function testGetFormattedFileSizeByGmp()
	{
		if (!extension_loaded('gmp')) {
			$this->markTestSkipped('GMP extension is not loaded.');
		}

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => gmp_strval(gmp_mul(gmp_pow('1024', '3'), '2')),
		));

		$this->assertEquals('2GB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => gmp_strval(gmp_mul(gmp_pow('1024', '4'), '2')),
		));

		$this->assertEquals('2TB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => gmp_strval(gmp_mul(gmp_pow('1024', '5'), '2')),
		));

		$this->assertEquals('2PB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => gmp_strval(gmp_mul(gmp_pow('1024', '6'), '2')),
		));

		$this->assertEquals('2EB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => gmp_strval(gmp_mul(gmp_pow('1024', '7'), '2')),
		));

		$this->assertEquals('2ZB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => gmp_strval(gmp_mul(gmp_pow('1024', '8'), '2')),
		));

		$this->assertEquals('2YB', $image->formattedFileSize);
	}

	public function testGetFormattedFileSizeWithByteScale()
	{
		$image = new Image(array(
			'byteScale' => 1,
			'fileSize'  => 100,
		));

		$this->assertEquals('100.0B', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 1,
			'fileSize'  => 1024,
		));

		$this->assertEquals('1.0KB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 1,
			'fileSize'  => 1024 * 2,
		));

		$this->assertEquals('2.0KB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 1,
			'fileSize'  => 1024 * 1024 * 2,
		));

		$this->assertEquals('2.0MB', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 1,
			'fileSize'  => 1024 * 1024 * 1024 * 2,
		));

		$this->assertEquals('2.0GB', $image->formattedFileSize);
	}

	public function testGetFormattedFileSizeWithByteUnits()
	{
		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 100,
			'byteUnits' => array(' Bytes', ' KiloBytes', ' MegaBytes', ' GigaBytes', ' TeraBytes', ' PetaBytes', ' ExaBytes', ' ZettaBytes', ' YottaBytes'),
		));

		$this->assertEquals('100 Bytes', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024,
			'byteUnits' => array(' Bytes', ' KiloBytes', ' MegaBytes', ' GigaBytes', ' TeraBytes', ' PetaBytes', ' ExaBytes', ' ZettaBytes', ' YottaBytes'),
		));

		$this->assertEquals('1 KiloBytes', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024 * 2,
			'byteUnits' => array(' Bytes', ' KiloBytes', ' MegaBytes', ' GigaBytes', ' TeraBytes', ' PetaBytes', ' ExaBytes', ' ZettaBytes', ' YottaBytes'),
		));

		$this->assertEquals('2 KiloBytes', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024 * 1024 * 2,
			'byteUnits' => array(' Bytes', ' KiloBytes', ' MegaBytes', ' GigaBytes', ' TeraBytes', ' PetaBytes', ' ExaBytes', ' ZettaBytes', ' YottaBytes'),
		));

		$this->assertEquals('2 MegaBytes', $image->formattedFileSize);

		$image = new Image(array(
			'byteScale' => 0,
			'fileSize'  => 1024 * 1024 * 1024 * 2,
			'byteUnits' => array(' Bytes', ' KiloBytes', ' MegaBytes', ' GigaBytes', ' TeraBytes', ' PetaBytes', ' ExaBytes', ' ZettaBytes', ' YottaBytes'),
		));

		$this->assertEquals('2 GigaBytes', $image->formattedFileSize);
	}

}
