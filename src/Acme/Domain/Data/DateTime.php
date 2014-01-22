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

/**
 * 日時
 *
 * @author k.holy74@gmail.com
 */
class DateTime implements DataInterface
{

	use DataTrait;

	/**
	 * @var \DateTime
	 */
	private $datetime;

	/**
	 * @var \DateTimeZone タイムゾーン
	 */
	private $timezone;

	/**
	 * @var string 出力用書式
	 */
	private $format;

	/**
	 * __construct()
	 *
	 * @param array プロパティの配列
	 */
	public function __construct(array $properties = [])
	{

		if (!isset($properties['timezone'])) {
			$properties['timezone'] = date_default_timezone_get();
		}

		if (!isset($properties['format'])) {
			$properties['format'] = 'Y-m-d H:i:s';
		}

		$this->initialize($properties);
	}

	/**
	 * __call
	 *
	 * @param string
	 * @param array
	 */
	public function __call($name, $args)
	{
		if (method_exists($this->datetime, $name)) {
			return call_user_func_array(array($this->datetime, $name), $args);
		}
		throw new \BadMethodCallException(
			sprintf('Undefined Method "%s" called.', $name)
		);
	}

	/**
	 * 日時をセットします。
	 *
	 * @param mixed
	 */
	public function setDatetime(\DateTime $datetime)
	{
		if (!isset($this->timezone)) {
			$this->timezone = $datetime->getTimezone();
		}
		$this->datetime = $datetime;
	}

	/**
	 * タイムゾーンをセットします。
	 *
	 * @param DateTimeZone
	 */
	private function setTimezone(\DateTimeZone $timezone)
	{
		$this->timezone = $timezone;
		if (isset($this->datetime)) {
			$this->datetime->setTimezone($timezone);
		}
	}

	/**
	 * 日時の出力用書式をセットします。
	 *
	 * @param string
	 */
	private function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * 現在の年を数値で返します。
	 *
	 * @return int 年 (4桁)
	 */
	public function year()
	{
		return (int)$this->datetime->format('Y');
	}

	/**
	 * 現在の月を数値で返します。
	 *
	 * @return int 月 (0-59)
	 */
	public function month()
	{
		return (int)$this->datetime->format('m');
	}

	/**
	 * 現在の日を数値で返します。
	 *
	 * @return int 日 (1-31)
	 */
	public function day()
	{
		return (int)$this->datetime->format('d');
	}

	/**
	 * 現在の時を数値で返します。
	 *
	 * @return int 時 (0-23)
	 */
	public function hour()
	{
		return (int)$this->datetime->format('H');
	}

	/**
	 * 現在の分を数値で返します。
	 *
	 * @return int 分 (0-59)
	 */
	public function minute()
	{
		return (int)$this->datetime->format('i');
	}

	/**
	 * 現在の秒を数値で返します。
	 *
	 * @return int 秒 (0-59)
	 */
	public function second()
	{
		return (int)$this->datetime->format('s');
	}

	/**
	 * UnixTimeを返します。
	 *
	 * @param int
	 */
	public function timestamp()
	{
		return (int)$this->datetime->format('U');
	}

	/**
	 * 現在の月の日数を数値で返します。
	 *
	 * @return int 日 (28-31)
	 */
	public function lastDay()
	{
		return (int)$this->datetime->format('t');
	}

	/**
	 * 現在の日時をデフォルトの書式文字列で返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->datetime->format($this->format);
	}

	/**
	 * __wakeup
	 *
	 * @param void
	 * @return void
	 */
	public function __wakeup()
	{
		$this->initialize(new \DateTime());
	}

	/**
	 * __sleep
	 *
	 * @param void
	 * @return void
	 */
	public function __sleep()
	{
		return array(
			array('datetime', 'timezone', 'format'),
		);
	}

}
