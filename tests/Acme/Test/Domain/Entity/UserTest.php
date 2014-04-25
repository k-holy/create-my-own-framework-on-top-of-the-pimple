<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Entity;

use Acme\Domain\Entity\User;

/**
 * Test for User
 *
 * @author k.holy74@gmail.com
 */
class UserTest extends \PHPUnit_Framework_TestCase
{

	public function testConstructWithProperties()
	{
		$createdAt = $this->getMockBuilder('Acme\Domain\Value\DateTime')
			->disableOriginalConstructor()
			->getMock();

		$updatedAt = $this->getMockBuilder('Acme\Domain\Value\DateTime')
			->disableOriginalConstructor()
			->getMock();

		$hashProcessor = $this->getMockBuilder('Acme\Security\HashProcessorInterface')
			->disableOriginalConstructor()
			->getMock();

		$user = new User(array(
			'id' => 1,
			'loginId' => 'login-id',
			'loginPassword' => 'hashed-password',
			'hashSalt' => 'salty',
			'nickname' => 'foo',
			'createdAt' => $createdAt,
			'updatedAt' => $updatedAt,
			'hashProcessor' => $hashProcessor,
		));

		$this->assertEquals(1, $user->id);
		$this->assertEquals('login-id', $user->loginId);
		$this->assertEquals('hashed-password', $user->loginPassword);
		$this->assertEquals('salty', $user->hashSalt);
		$this->assertEquals('foo', $user->nickname);
		$this->assertInstanceOf('Acme\Domain\Value\DateTime', $user->createdAt);
		$this->assertInstanceOf('Acme\Domain\Value\DateTime', $user->updatedAt);
		$this->assertInstanceOf('Acme\Security\HashProcessorInterface', $user->hashProcessor);
	}

	public function testVerifyPassword()
	{
		$hashProcessor = $this->getMockBuilder('Acme\Security\HashProcessorInterface')
			->disableOriginalConstructor()
			->getMock();

		$hashProcessor->expects($this->any())
			->method('hash')
			->will($this->returnArgument(0));

		$user = new User(array(
			'loginPassword' => 'hashed-password',
			'hashSalt' => 'salty',
			'hashProcessor' => $hashProcessor,
		));

		$this->assertTrue($user->verifyPassword('hashed-password'));
	}

}
