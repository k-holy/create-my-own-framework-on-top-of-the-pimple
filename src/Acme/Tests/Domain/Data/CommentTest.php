<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Tests\Domain\Data;

use Acme\Domain\Data\Comment;

/**
 * Test for Comment
 *
 * @author k.holy74@gmail.com
 */
class CommentTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructWithAttributes()
	{
		$currentDateTime = new \DateTime(sprintf('@%d', time()));
		$comment = new Comment([
			'author'    => 'foo',
			'comment'   => 'bar',
			'posted_at' => $currentDateTime,
		], [
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$this->assertEquals('foo', $comment->author);
		$this->assertEquals('bar', $comment->comment);
		$this->assertEquals(
			$currentDateTime->getTimestamp(),
			$comment->posted_at->getTimestamp()
		);
	}

	public function testSetAttributeByArrayAccess()
	{
		$comment = new Comment([],[
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$comment['author'] = 'foo';
		$comment['comment'] = 'bar';
		$this->assertEquals('foo', $comment['author']);
		$this->assertEquals('bar', $comment['comment']);
	}

	public function testSetAttributeByProperty()
	{
		$comment = new Comment([],[
			'timezone' => new \DateTimeZone('Asia/Tokyo'),
		]);
		$comment->author = 'foo';
		$comment->comment = 'bar';
		$this->assertEquals('foo', $comment->author);
		$this->assertEquals('bar', $comment->comment);
	}

}
