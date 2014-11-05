<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Value;

use Acme\Domain\Value\Filepath;

/**
 * Test for Filepath
 *
 * @author k.holy74@gmail.com
 */
class FilepathTest extends \PHPUnit_Framework_TestCase
{

	public function testGetDirname()
	{
		$path = new Filepath('path/to/file.is.jpeg');
		$this->assertEquals('path/to', $path->dirname);
		$this->assertEquals('path/to', $path->getDirname());
	}

	public function testGetBasename()
	{
		$path = new Filepath('path/to/file.is.jpeg');
		$this->assertEquals('file.is.jpeg', $path->basename);
		$this->assertEquals('file.is.jpeg', $path->getBasename());
	}

	public function testGetExtension()
	{
		$path = new Filepath('path/to/file.is.jpeg');
		$this->assertEquals('jpeg', $path->extension);
		$this->assertEquals('jpeg', $path->getExtension());
	}

	public function testRegularizeDirectorySeparatorOnWindows()
	{
		if (DIRECTORY_SEPARATOR !== '\\') {
			$this->markTestSkipped('DIRECTORY_SEPARATOR is not "\\"');
		}
		$path = new Filepath('path\\to/file.is.jpeg');
		$this->assertEquals('path/to/file.is.jpeg', $path->getValue());
	}

	public function testRemoveTopDirectorySeparator()
	{
		$path = new Filepath('/path/to/file.is.jpeg');
		$this->assertEquals('path/to/file.is.jpeg', $path->getValue());
	}

	public function testMultibyteValue()
	{
		$path = new Filepath('日本語/パス/日本語.jpeg');
		$this->assertEquals('日本語/パス', $path->dirname);
		$this->assertEquals('日本語.jpeg', $path->basename);
		$this->assertEquals('jpeg', $path->extension);
	}

	public function testGetUrlencodedValue()
	{
		$path = new Filepath('日本語/パス/日本語.jpeg');
		$this->assertEquals(
			sprintf('%s/%s/%s.jpeg',
				rawurlencode('日本語'),
				rawurlencode('パス'),
				rawurlencode('日本語')
			),
			$path->urlencode()
		);
	}

	public function testIsImage()
	{
		$path = new Filepath('path/to/file.is.jpeg');
		$this->assertTrue($path->isImage());
		$path = new Filepath('path/to/file.is.JPEG');
		$this->assertTrue($path->isImage());
		$path = new Filepath('path/to/file.is.Jpeg');
		$this->assertTrue($path->isImage());
	}

	public function testIsDir()
	{
		$path = new Filepath('path/to/directory/');
		$this->assertTrue($path->isDir());
	}

	public function testNotIsDir()
	{
		$path = new Filepath('path/to/file.is.jpeg');
		$this->assertFalse($path->isDir());
	}

	public function testGetDirnameOfSingleLeafFile()
	{
		$path = new Filepath('file.is.jpeg');
		$this->assertEquals('.', $path->dirname);
		$this->assertEquals('.', $path->getDirname());
	}

	public function testGetExtensionOfSingleLeafFile()
	{
		$path = new Filepath('file.is.jpeg');
		$this->assertEquals('jpeg', $path->extension);
		$this->assertEquals('jpeg', $path->getExtension());
	}

	public function testGetBasenameOfSingleLeafFile()
	{
		$path = new Filepath('file.is.jpeg');
		$this->assertEquals('file.is.jpeg', $path->basename);
		$this->assertEquals('file.is.jpeg', $path->getBasename());
	}

	public function testGetDirnameOfSingleLeafDirectory()
	{
		$path = new Filepath('directory/');
		$this->assertEquals('.', $path->dirname);
		$this->assertEquals('.', $path->getDirname());
	}

	public function testGetExtensionOfSingleLeafDirectory()
	{
		$path = new Filepath('directory/');
		$this->assertNull($path->extension);
		$this->assertNull($path->getExtension());
	}

	public function testGetBasenameOfSingleLeafDirectory()
	{
		$path = new Filepath('directory/');
		$this->assertEquals('directory', $path->basename);
		$this->assertEquals('directory', $path->getBasename());
	}

	public function testGetIterator()
	{
		$path = new Filepath('日本語/パス/日本語.jpeg');
		$iterator = $path->getIterator();
		$this->assertInstanceOf('\Iterator', $iterator);
		$this->assertInstanceOf('\Countable', $iterator);
		$iterator->rewind();
		foreach ($iterator as $i => $current) {
			$this->assertInstanceOf('Acme\Domain\Value\Filepath', $current);
			switch ($i) {
			case 0:
				$this->assertEquals('日本語', $current->getValue());
				$this->assertEquals('日本語', $current->getBasename());
				$this->assertTrue($current->isDir());
				break;
			case 1:
				$this->assertEquals('日本語/パス', $current->getValue());
				$this->assertEquals('パス', $current->getBasename());
				$this->assertTrue($current->isDir());
				break;
			case 2:
				$this->assertEquals('日本語/パス/日本語.jpeg', $current->getValue());
				$this->assertEquals('日本語.jpeg', $current->getBasename());
				$this->assertFalse($current->isDir());
				break;
			}
		}
	}

	public function testIteratorAggregate()
	{
		$path = new Filepath('日本語/パス/日本語.jpeg');
		foreach ($path as $i => $current) {
			$this->assertInstanceOf('Acme\Domain\Value\Filepath', $current);
			switch ($i) {
			case 0:
				$this->assertEquals('日本語', $current->getValue());
				$this->assertEquals('日本語', $current->getBasename());
				$this->assertTrue($current->isDir());
				break;
			case 1:
				$this->assertEquals('日本語/パス', $current->getValue());
				$this->assertEquals('パス', $current->getBasename());
				$this->assertTrue($current->isDir());
				break;
			case 2:
				$this->assertEquals('日本語/パス/日本語.jpeg', $current->getValue());
				$this->assertEquals('日本語.jpeg', $current->getBasename());
				$this->assertFalse($current->isDir());
				break;
			}
		}
	}

}
