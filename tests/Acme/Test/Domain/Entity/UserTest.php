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
		$createdAt = $this->getMockBuilder('Acme\Value\DateTime')
			->disableOriginalConstructor()
			->getMock();

		$updatedAt = $this->getMockBuilder('Acme\Value\DateTime')
			->disableOriginalConstructor()
			->getMock();

		$passwordProcessor = $this->getMockBuilder('Acme\Security\PasswordProcessorInterface')
			->disableOriginalConstructor()
			->getMock();

		$user = new User(array(
			'id' => 1,
			'loginId' => 'login-id',
			'loginPassword' => 'encoded-password',
			'hashSalt' => 'salty',
			'name' => 'foo',
			'createdAt' => $createdAt,
			'updatedAt' => $updatedAt,
			'passwordProcessor' => $passwordProcessor,
		));

		$this->assertEquals(1, $user->id);
		$this->assertEquals('login-id', $user->loginId);
		$this->assertEquals('encoded-password', $user->loginPassword);
		$this->assertEquals('salty', $user->hashSalt);
		$this->assertInstanceOf('Acme\Value\DateTime', $user->createdAt);
		$this->assertInstanceOf('Acme\Value\DateTime', $user->updatedAt);
		$this->assertInstanceOf('Acme\Security\PasswordProcessorInterface', $user->passwordProcessor);
	}

	public function testVerifyPassword()
	{
		$passwordProcessor = $this->getMockBuilder('Acme\Security\PasswordProcessorInterface')
			->disableOriginalConstructor()
			->getMock();

		$passwordProcessor->expects($this->any())
			->method('encode')
			->will($this->returnArgument(0));

		$user = new User(array(
			'loginPassword' => 'encoded-password',
			'hashSalt' => 'salty',
			'passwordProcessor' => $passwordProcessor,
		));

		$this->assertTrue($user->verifyPassword('encoded-password'));
	}

}
