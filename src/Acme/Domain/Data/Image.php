<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Data;

use Acme\DateTime;

/**
 * 画像
 *
 * @author k.holy74@gmail.com
 */
class Image implements \ArrayAccess, \IteratorAggregate
{

	use DataTrait;

	/**
	 * @var string 日付書式
	 */
	private $datetimeFormat;

	/**
	 * @var \DateTimeZone タイムゾーン
	 */
	private $timezone;

	/**
	 * @var int バイト表記の小数点以下桁数
	 */
	private $byteScale;

	/**
	 * @var array of string バイト表記単位の配列
	 */
	private $byteUnits;

	/**
	 * @var array 属性値の配列
	 */
	private $attributes = [
		'id'           => null,
		'file_name'    => null,
		'file_size'    => null,
		'encoded_data' => null,
		'mime_type'    => null,
		'width'        => null,
		'height'       => null,
		'created_at'   => null,
	];

	public function __construct($attributes = array(), $options = array())
	{
		$this->initialize($attributes, $options);
	}

	/**
	 * プロパティを初期化します。
	 *
	 * @param array プロパティ
	 * @return self
	 */
	public function initialize($attributes = array(), $options = array())
	{
		if (!isset($options['timezone'])) {
			throw new \InvalidArgumentException('Required option "timezone" is not appointed.');
		}
		$this->setTimezone($options['timezone']);

		$this->datetimeFormat = isset($options['datetimeFormat']) ? $options['datetimeFormat'] : 'Y-m-d H:i:s';

		$this->byteScale = isset($options['byteScale']) ? $options['byteScale'] : 1;

		$this->byteUnits = isset($options['byteUnits']) ? $options['byteUnits'] : ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];

		$this->attributes($attributes);

		return $this;
	}

	/**
	 * DateTimeZoneオブジェクトをセットします。
	 *
	 * @param \DateTimeZone タイムゾーン
	 */
	public function setTimezone(\DateTimeZone $timezone)
	{
		$this->timezone = $timezone;
	}

	/**
	 * setter for created_at
	 *
	 * @param mixed
	 */
	public function set_created_at($datetime)
	{
		if (false === ($datetime instanceof DateTime)) {
			$datetime = new DateTime($datetime);
		}
		$datetime->setTimezone($this->timezone);
		$this->attributes['created_at'] = $datetime->getTimestamp(); // 実体はUnixTimestampで保持
	}

	/**
	 * getter for created_at
	 *
	 * @return \Acme\DateTime
	 */
	public function get_created_at()
	{
		if (isset($this->attributes['created_at'])) {
			$datetime = new DateTime($this->attributes['created_at'], $this->datetimeFormat); // UnixTimestampで保持している値をDateTimeクラスで変換して出力
			$datetime->setTimezone($this->timezone);
			return $datetime;
		}
		return null;
	}

	/**
	 * getter for Data URI
	 *
	 * @return string Data URI
	 */
	public function getDataUri()
	{
		if (isset($this->attributes['mime_type']) && isset($this->attributes['encoded_data'])) {
			return sprintf('data:%s;base64,%s', $this->attributes['mime_type'], $this->attributes['encoded_data']);
		}
		return null;
	}

	/**
	 * バイト単位で書式化されたファイルサイズを返します。
	 *
	 * @return string 書式化したファイルサイズ
	 */
	public function getFormattedFileSize()
	{
		if (isset($this->attributes['file_size'])) {
			$number = $this->attributes['file_size'];
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