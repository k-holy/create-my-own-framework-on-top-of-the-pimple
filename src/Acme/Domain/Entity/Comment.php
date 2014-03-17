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
use Acme\Domain\Entity\Image;

use Acme\Domain\Value\DateTime;

/**
 * コメント
 *
 * @author k.holy74@gmail.com
 */
class Comment implements EntityInterface, \ArrayAccess, \IteratorAggregate
{

	use EntityTrait;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $author;

	/**
	 * @var string
	 */
	private $comment;

	/**
	 * @var int
	 */
	private $imageId;

	/**
	 * @var Acme\Domain\Value\DateTime
	 */
	private $postedAt;

	/**
	 * @var Acme\Domain\Entity\Image
	 */
	private $image;

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
	 * postedAtの値をセットします。
	 *
	 * @param Acme\Domain\Value\DateTime
	 */
	private function setPostedAt(DateTime $postedAt)
	{
		$this->postedAt = $postedAt;
	}

	/**
	 * imageの値をセットします。
	 *
	 * @param Acme\Domain\Entity\Image
	 */
	private function setImage(Image $image = null)
	{
		$this->image = $image;
	}

}
