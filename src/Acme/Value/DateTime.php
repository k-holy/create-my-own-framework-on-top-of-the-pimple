<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Value;

use Acme\Value\ValueInterface;
use Acme\Value\ValueTrait;

/**
 * 日時
 *
 * @author k.holy74@gmail.com
 */
class DateTime implements ValueInterface, \ArrayAccess, \IteratorAggregate
{

	use ValueTrait;

	/**
	 * @var \DateTime
	 */
	private $value;

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
	 * @param mixed 値
	 * @param array オプション
	 */
	public function __construct($value = null, array $options = array())
	{

		if ($value === null) {
			$value = new \DateTime();
		} else {
			if ($value instanceof \DateTime) {
				if (!isset($options['timezone'])) {
					$options['timezone'] = $value->getTimezone();
				}
			} elseif (is_int($value) || ctype_digit($value)) {
				$value = new \DateTime(sprintf('@%d', $value));
			} elseif (is_string($value)) {
				$value = new \DateTime($value);
			}
			if (false === ($value instanceof \DateTime)) {
				throw new \InvalidArgumentException(
					sprintf('Invalid type:%s', (is_object($value))
						? get_class($value)
						: gettype($value)
					)
				);
			}
		}

		if (!isset($options['timezone'])) {
			$options['timezone'] = new \DateTimeZone(date_default_timezone_get());
		} else {
			if (is_string($options['timezone'])) {
				$options['timezone'] = new \DateTimeZone($options['timezone']);
			}
			if (false === ($options['timezone'] instanceof \DateTimeZone)) {
				throw new \InvalidArgumentException(
					sprintf('Invalid type:%s', (is_object($options['timezone']))
						? get_class($options['timezone'])
						: gettype($options['timezone'])
					)
				);
			}
		}

		if (!isset($options['format'])) {
			$options['format'] = 'Y-m-d H:i:s';
		}
		$this->initialize($value, $options);
	}

	/**
	 * 現在の日時をデフォルトの書式文字列で返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->value->format($this->format);
	}

	/**
	 * __call
	 *
	 * @param string
	 * @param array
	 */
	public function __call($name, $args)
	{
		if (method_exists($this->value, $name)) {
			return call_user_func_array(array($this->value, $name), $args);
		}
		throw new \BadMethodCallException(
			sprintf('Undefined Method "%s" called.', $name)
		);
	}

	/**
	 * タイムゾーンをセットします。
	 *
	 * @param mixed
	 */
	private function setTimezone(\DateTimeZone $timezone)
	{
		if (isset($this->value)) {
			$this->value->setTimezone($timezone);
		}
		$this->timezone = $timezone;
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
	public function getYear()
	{
		return (int)$this->value->format('Y');
	}

	/**
	 * 現在の月を数値で返します。
	 *
	 * @return int 月 (0-59)
	 */
	public function getMonth()
	{
		return (int)$this->value->format('m');
	}

	/**
	 * 現在の日を数値で返します。
	 *
	 * @return int 日 (1-31)
	 */
	public function getDay()
	{
		return (int)$this->value->format('d');
	}

	/**
	 * 現在の時を数値で返します。
	 *
	 * @return int 時 (0-23)
	 */
	public function getHour()
	{
		return (int)$this->value->format('H');
	}

	/**
	 * 現在の分を数値で返します。
	 *
	 * @return int 分 (0-59)
	 */
	public function getMinute()
	{
		return (int)$this->value->format('i');
	}

	/**
	 * 現在の秒を数値で返します。
	 *
	 * @return int 秒 (0-59)
	 */
	public function getSecond()
	{
		return (int)$this->value->format('s');
	}

	/**
	 * UnixTimeを返します。
	 *
	 * @return int
	 */
	public function getTimestamp()
	{
		return (int)$this->value->format('U');
	}

	/**
	 * DateTimeを返します。
	 *
	 * @return \DateTime
	 */
	public function getDatetime()
	{
		return $this->value;
	}

	/**
	 * 現在の月の日数を数値で返します。
	 *
	 * @return int 日 (28-31)
	 */
	public function getLastday()
	{
		return (int)$this->value->format('t');
	}

}
