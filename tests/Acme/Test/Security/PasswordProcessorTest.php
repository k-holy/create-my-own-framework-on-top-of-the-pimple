<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Security;

use Acme\Security\PasswordProcessor;

/**
 * Test for PasswordProcessor
 *
 * @author k.holy74@gmail.com
 */
class PasswordProcessorTest extends \PHPUnit_Framework_TestCase
{

	public function testCreateHashSalt()
	{
		$processor = new PasswordProcessor(array(
			'algorithm'  => 'sha256',
			'saltLength' => 500,
			'saltChars'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		));
		$salt = $processor->createHashSalt();
		$this->assertEquals(500, strlen($salt));
		$this->assertTrue(strspn($salt, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') === strlen($salt));
	}

	public function testCreateRandomPassword()
	{
		$processor = new PasswordProcessor(array(
			'randomPasswordLength' => 500,
			'randomPasswordChars'  => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		));
		$password = $processor->createRandomPassword();
		$this->assertEquals(500, strlen($password));
		$this->assertTrue(strspn($password, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') === strlen($password));
	}

	public function testEncodeIsMatch()
	{
		$processor1 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		$processor2 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		// 同一アルゴリズム、同一ストレッチ回数、同一パスワード、同一ハッシュソルトのエンコード結果は等しい
		$this->assertEquals(
			$processor1->encode('develop', 'test'),
			$processor2->encode('develop', 'test')
		);
		$this->assertEquals(
			$processor1->encode('foo', 'bar'),
			$processor2->encode('foo', 'bar')
		);
	}

	public function testEncodeIsNotMatchWhenPasswordIsNotSame()
	{
		$processor1 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		$processor2 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		// 異なったパスワード
		$this->assertNotEquals(
			$processor1->encode('develop', 'test'),
			$processor2->encode('another_develop', 'test')
		);
	}

	public function testEncodeIsNotMatchWhenHashSaltIsNotSame()
	{
		$processor1 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		$processor2 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		// 異なったハッシュソルト
		$this->assertNotEquals(
			$processor1->encode('develop', 'test'),
			$processor2->encode('develop', 'another_test')
		);
	}

	public function testEncodeIsNotMatchWhenAlgorhythmIsNotSame()
	{
		$processor1 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		$processor2 = new PasswordProcessor(array(
			'algorithm'       => 'sha384',
			'stretchingCount' => 100,
		));

		// 異なったアルゴルズム
		$this->assertNotEquals(
			$processor1->encode('develop', 'test'),
			$processor2->encode('develop', 'test')
		);
	}

	public function testEncodeIsNotMatchWhenStretchingCountIsNotSame()
	{
		$processor1 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 100,
		));

		$processor2 = new PasswordProcessor(array(
			'algorithm'       => 'sha256',
			'stretchingCount' => 10,
		));

		// 異なったストレッチ回数
		$this->assertNotEquals(
			$processor1->encode('develop', 'test'),
			$processor2->encode('develop', 'test')
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testEncodeRaiseExceptionWhenUnsupportedAlgorhythm()
	{
		$processor = new PasswordProcessor(array(
			'algorithm'       => 'unsupported-algorithm',
			'stretchingCount' => 100,
		));
		$processor->encode('develop', 'test');
	}

}
