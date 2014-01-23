<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Data;

use Acme\Domain\Data\Comment;
use Acme\Domain\Data\DateTime;

/**
 * Test for Comment
 *
 * @author k.holy74@gmail.com
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructWithProperties()
	{
		$postedAt = $this->getMock('Acme\Domain\Data\DateTime');
		$image = $this->getMock('Acme\Domain\Data\Image');

		$comment = new Comment(array(
			'id'       => 1,
			'author'   => 'foo',
			'comment'  => 'bar',
			'imageId'  => 2,
			'postedAt' => $postedAt,
			'image'    => $image,
		));

		$this->assertEquals(1, $comment->id);
		$this->assertEquals('foo', $comment->author);
		$this->assertEquals('bar', $comment->comment);
		$this->assertEquals(2, $comment->imageId);
		$this->assertInstanceOf('Acme\Domain\Data\DateTime', $comment->postedAt);
		$this->assertInstanceOf('Acme\Domain\Data\Image', $comment->image);
	}

}
