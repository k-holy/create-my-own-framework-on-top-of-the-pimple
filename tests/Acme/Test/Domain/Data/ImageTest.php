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
		$fileSize = $this->getMock('Acme\Domain\Data\Byte');

		$image = new Image(array(
			'id'          => '1',
			'fileName'    => 'foo',
			'fileSize'    => $fileSize,
			'encodedData' => 'encoded-data',
			'mimeType'    => 'image/png',
			'createdAt'   => $createdAt,
		));

		$this->assertEquals('1', $image->id);
		$this->assertEquals('foo', $image->fileName);
		$this->assertEquals('encoded-data', $image->encodedData);
		$this->assertEquals('image/png', $image->mimeType);
		$this->assertInstanceOf('Acme\Domain\Data\Byte', $image->fileSize);
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

}
