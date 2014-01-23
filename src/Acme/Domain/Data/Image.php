<?php
/**
 * ドメインデータ
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Data;

use Acme\Domain\Data\DataInterface;
use Acme\Domain\Data\DataTrait;
use Acme\Domain\Data\DateTime;

/**
 * 画像
 *
 * @author k.holy74@gmail.com
 */
class Image implements DataInterface, \ArrayAccess, \IteratorAggregate
{

	use DataTrait;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $fileName;

	/**
	 * @var int
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
	 * @var Acme\Domain\Data\DateTime
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
	 * createdAtの値をセットします。
	 *
	 * @param Acme\Domain\Data\DateTime
	 */
	private function setCreatedAt(DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
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

	/**
	 * バイト単位で書式化されたファイルサイズを返します。
	 *
	 * @return string 書式化したファイルサイズ
	 */
	public function getFormattedFileSize()
	{
		if (isset($this->fileSize)) {
			$number = $this->fileSize;
			$unit = '';
			foreach ($this->byteUnits as $unit) {
				if ($number < 1024) {
					break;
				}
				$number = $number / 1024;
			}
			return (isset($this->byteScale))
				? number_format($number, $this->byteScale) . $unit
				: number_format($number) . $unit;
		}
		return null;
	}

}
