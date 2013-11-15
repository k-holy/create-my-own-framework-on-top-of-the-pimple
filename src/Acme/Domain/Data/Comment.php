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
 * コメント
 *
 * @author k.holy74@gmail.com
 */
class Comment implements \ArrayAccess, \IteratorAggregate
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
	 * @var array 属性値の配列
	 */
	private $attributes = [
		'id'        => null,
		'author'    => null,
		'comment'   => null,
		'image_id'  => null,
		'posted_at' => null,
		'image'     => null, // 画像
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
	 * setter for posted_at
	 *
	 * @param mixed
	 */
	public function set_posted_at($datetime)
	{
		if (false === ($datetime instanceof DateTime)) {
			$datetime = new DateTime($datetime);
		}
		$datetime->setTimezone($this->timezone);
		$this->attributes['posted_at'] = $datetime->getTimestamp(); // 実体はUnixTimestampで保持
	}

	/**
	 * getter for posted_at
	 *
	 * @return \Acme\DateTime
	 */
	public function get_posted_at()
	{
		if (isset($this->attributes['posted_at'])) {
			$datetime = new DateTime($this->attributes['posted_at'], $this->datetimeFormat); // UnixTimestampで保持している値をDateTimeクラスで変換して出力
			$datetime->setTimezone($this->timezone);
			return $datetime;
		}
		return null;
	}

}
