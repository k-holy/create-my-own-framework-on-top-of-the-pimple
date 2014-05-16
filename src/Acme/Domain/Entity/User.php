<?php
/**
 * エンティティオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Entity;

use Acme\Domain\Entity\EntityInterface;
use Acme\Domain\Entity\EntityTrait;

use Acme\Domain\Value\DateTime;

use Acme\Security\HashProcessorInterface;

/**
 * ユーザー
 *
 * @author k.holy74@gmail.com
 */
class User implements EntityInterface, \ArrayAccess, \IteratorAggregate
{

	use EntityTrait;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $loginId;

	/**
	 * @var string
	 */
	private $loginPassword;

	/**
	 * @var string
	 */
	private $hashSalt;

	/**
	 * @var string
	 */
	private $nickname;

	/**
	 * @var Acme\Domain\Value\DateTime
	 */
	private $createdAt;

	/**
	 * @var Acme\Domain\Value\DateTime
	 */
	private $updatedAt;

	/**
	 * @var Acme\Security\HashProcessorInterface
	 */
	private $hashProcessor;

	/**
	 * このオブジェクトのIDを返します。
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * 登録日をセットします。
	 *
	 * @param Acme\Domain\Value\DateTime
	 */
	private function setCreatedAt(DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * 更新日をセットします。
	 *
	 * @param Acme\Domain\Value\DateTime
	 */
	private function setUpdatedAt(DateTime $updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * パスワード処理クラスをセットします。
	 *
	 * @param Acme\Security\HashProcessorInterface ハッシュ処理インタフェース
	 */
	private function setHashProcessor(HashProcessorInterface $hashProcessor)
	{
		$this->hashProcessor = $hashProcessor;
	}

	/**
	 * パスワードが合致するか検証します。
	 *
	 * @param string パスワード
	 * @return bool
	 */
	public function verifyPassword($password)
	{
		return ($this->loginPassword === $this->hashProcessor->hash($password, $this->hashSalt));
	}

}
