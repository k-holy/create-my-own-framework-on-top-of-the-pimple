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
use Acme\Domain\Value\Byte;

/**
 * 画像
 *
 * @author k.holy74@gmail.com
 */
class Image implements EntityInterface, \ArrayAccess, \IteratorAggregate
{

	use EntityTrait;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $fileName;

	/**
	 * @var Acme\Domain\Value\Byte
	 */
	private $fileSize;

	/**
	 * @var string
	 */
	private $encodedData;

	/**
	 * @var string
	 */
	private $mimeType;

	/**
	 * @var int
	 */
	private $width;

	/**
	 * @var int
	 */
	private $height;

	/**
	 * @var Acme\Domain\Value\DateTime
	 */
	private $createdAt;

	/**
	 * @var int バイト表記の小数点以下桁数
	 */
	private $byteScale;

	/**
	 * @var array of string バイト表記単位の配列
	 */
	private $byteUnits;

	/**
	 * __construct()
	 *
	 * @param array プロパティの配列
	 */
	public function __construct(array $properties = [])
	{

		if (!isset($properties['byteScale'])) {
			$properties['byteScale'] = 1;
		}

		if (!isset($properties['byteUnits'])) {
			$properties['byteUnits'] = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
		}

		$this->initialize($properties);
	}

	/**
	 * 登録日時をセットします。
	 *
	 * @param Acme\Domain\Value\DateTime
	 */
	private function setCreatedAt(DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
	}

	/**
	 * ファイルサイズをセットします。
	 *
	 * @param Acme\Domain\Value\DateTime
	 */
	private function setFileSize(Byte $fileSize)
	{
		$this->fileSize = $fileSize;
	}

	/**
	 * エンコードされたデータを Data URI 形式で返します。
	 *
	 * @return string Data URI
	 */
	public function getDataUri()
	{
		return (isset($this->mimeType) && isset($this->encodedData))
			? sprintf('data:%s;base64,%s', $this->mimeType, $this->encodedData)
			: null;
	}

}
