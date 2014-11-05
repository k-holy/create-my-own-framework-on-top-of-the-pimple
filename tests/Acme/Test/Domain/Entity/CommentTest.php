<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Entity;

use Acme\Domain\Entity\Comment;

/**
 * Test for Comment
 *
 * @author k.holy74@gmail.com
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructWithProperties()
	{
		$postedAt = $this->getMockBuilder('Acme\Domain\Value\DateTime')
			->disableOriginalConstructor()
			->getMock();

		$image = $this->getMockBuilder('Acme\Domain\Entity\Image')
			->disableOriginalConstructor()
			->getMock();

		$comment = new Comment(array(
			'id'       => 1,
			'author'   => 'foo',
			'comment'  => 'bar',
			'postedAt' => $postedAt,
			'image'    => $image,
		));

		$this->assertEquals(1, $comment->id);
		$this->assertEquals('foo', $comment->author);
		$this->assertEquals('bar', $comment->comment);
		$this->assertInstanceOf('Acme\Domain\Value\DateTime', $comment->postedAt);
		$this->assertInstanceOf('Acme\Domain\Entity\Image', $comment->image);
	}

}
