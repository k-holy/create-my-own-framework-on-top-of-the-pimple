<?php
/**
 * ドメインデータ
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Data;

use Acme\Domain\Data\DataInterface;
use Acme\Domain\Data\DataTrait;
use Acme\Domain\Data\DateTime;
use Acme\Domain\Data\Image;

/**
 * コメント
 *
 * @author k.holy74@gmail.com
 */
class Comment implements DataInterface, \ArrayAccess, \IteratorAggregate
{

	use DataTrait;

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
	 * @var Acme\Domain\Data\DateTime
	 */
	private $postedAt;

	/**
	 * @var Acme\Domain\Data\Image
	 */
	private $image;

	/**
	 * postedAtの値をセットします。
	 *
	 * @param Acme\Domain\Data\DateTime
	 */
	private function setPostedAt(DateTime $postedAt)
	{
		$this->postedAt = $postedAt;
	}

	/**
	 * imageの値をセットします。
	 *
	 * @param Acme\Domain\Data\Image
	 */
	private function setImage(Image $image = null)
	{
		$this->image = $image;
	}

}
