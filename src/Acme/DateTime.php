<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme;

/**
 * DateTimeクラス
 *
 * @author k.holy74@gmail.com
 */
class DateTime implements \ArrayAccess
{

	/**
	 * @var \DateTime
	 */
	protected $datetime;

	/**
	 * @var string 日時の書式
	 */
	protected $format;

	/**
	 * コンストラクタ
	 *
	 * @param string|int|\DateTime 日時
	 * @param string 日時の書式
	 */
	public function __construct($datetime, $format = null)
	{
		if (is_int($datetime) || ctype_digit($datetime)) {
			$datetime = new \DateTime(sprintf('@%d', $datetime));
		} elseif (is_string($datetime)) {
			$datetime = new \DateTime($datetime);
		}
		if (false === ($datetime instanceof \DateTime)) {
			throw new \InvalidArgumentException(
				sprintf('Invalid type:%s', (is_object($datetime))
					? get_class($datetime)
					: gettype($datetime)
				)
			);
		}
		$this->datetime = $datetime;
		$this->format = (isset($format)) ? $format : 'Y-m-d H:i:s';
	}

	/**
	 * DateTimeオブジェクトを返します
	 *
	 * @return \DateTime
	 */
	public function getDateTime()
	{
		return $this->datetime;
	}

	/**
	 * 日時の書式をセットします。
	 *
	 * @param string 日時の書式
	 * @return $this
	 */
	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}

	/**
	 * タイムゾーンをセットします。
	 *
	 * @param string|\DateTimeZone タイムゾーン
	 * @return $this
	 */
	public function setTimeZone($timeZone)
	{
		if (is_string($timeZone)) {
			$timeZone = new \DateTimeZone($timeZone);
		}
		if (false === ($timeZone instanceof \DateTimeZone)) {
			throw new \InvalidArgumentException(
				sprintf('Invalid type:%s', (is_object($timeZone))
					? get_class($timeZone)
					: gettype($timeZone)
				)
			);
		}
		$this->datetime->setTimeZone($timeZone);
		return $this;
	}

	/**
	 * 書式化した日付文字列を返します。
	 *
	 * @return string 書式化した日付文字列
	 */
	public function format($format)
	{
		return $this->datetime->format($format);
	}

	/**
	 * UTCからのタイムゾーンオフセット秒数を返します。
	 *
	 * @return int UTCからのタイムゾーンオフセット秒数
	 */
	public function getOffset()
	{
		return $this->datetime->getOffset();
	}

	/**
	 * Unixタイムスタンプを返します。
	 *
	 * @return int Unixタイムスタンプ
	 */
	public function getTimestamp()
	{
		return $this->datetime->getTimestamp();
	}

	/**
	 * タイムゾーンを返します。
	 *
	 * @return \DateTimeZone タイムゾーン
	 */
	public function getTimezone()
	{
		return $this->datetime->getTimezone();
	}

	/**
	 * 現在の年を数値で返します。
	 *
	 * @return int 年 (4桁)
	 */
	public function year()
	{
		return (int)$this->format('Y');
	}

	/**
	 * 現在の月を数値で返します。
	 *
	 * @return int 月 (0-59)
	 */
	public function month()
	{
		return (int)$this->format('m');
	}

	/**
	 * 現在の日を数値で返します。
	 *
	 * @return int 日 (1-31)
	 */
	public function day()
	{
		return (int)$this->format('d');
	}

	/**
	 * 現在の時を数値で返します。
	 *
	 * @return int 時 (0-23)
	 */
	public function hour()
	{
		return (int)$this->format('H');
	}

	/**
	 * 現在の分を数値で返します。
	 *
	 * @return int 分 (0-59)
	 */
	public function minute()
	{
		return (int)$this->format('i');
	}

	/**
	 * 現在の秒を数値で返します。
	 *
	 * @return int 秒 (0-59)
	 */
	public function second()
	{
		return (int)$this->format('s');
	}

	/**
	 * UnixTimeを返します。
	 *
	 * @param int
	 */
	public function timestamp()
	{
		return (int)$this->format('U');
	}

	/**
	 * 現在の月の日数を数値で返します。
	 *
	 * @return int 日 (28-31)
	 */
	public function lastDay()
	{
		return (int)$this->format('t');
	}

	/**
	 * 現在の日時をデフォルトの書式文字列で返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->format($this->format);
	}

	/**
	 * メソッドへのプロパティアクセッサ
	 *
	 * @param string プロパティ名
	 */
	public function __get($name)
	{
		if (method_exists($this, $name)) {
			return $this->{$name}();
		}
		throw new \BadMethodCallException(
			sprintf('The property "%s" could not defined.', $name)
		);
	}

	/**
	 * __set
	 *
	 * @param string
	 * @param mixed
	 */
	public function __set($name, $value)
	{
		throw new \BadMethodCallException(
			sprintf('The property "%s" is read only.', $name)
		);
	}

	/**
	 * __wakeup
	 *
	 * @param void
	 * @return void
	 */
	public function __wakeup()
	{
		$this->initialize(time());
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return method_exists($this, $name);
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		if (method_exists($this, $name)) {
			return $this->{$name}();
		}
		throw new \BadMethodCallException(
			sprintf('The offset "%s" could not defined.', $name)
		);
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($name, $value)
	{
		throw new \BadMethodCallException(
			sprintf('The offset "%s" is read only.', $name)
		);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($name)
	{
		throw new \BadMethodCallException(
			sprintf('The offset "%s" is read only.', $name)
		);
	}

}
