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
	private $datetime;

	/**
	 * @var string 日時の書式
	 */
	private $format;

	/**
	 * コンストラクタ
	 *
	 * @param string | \DateTime 日時
	 * @param string 日時の書式
	 */
	public function __construct($datetime, $format = null)
	{
		if ($datetime instanceof \DateTime) {
			$this->datetime = $datetime;
		} elseif (is_int($datetime)) {
			$this->datetime = new \DateTime();
			$this->datetime->setTimestamp($datetime);
		} elseif (is_string($datetime)) {
			$this->datetime = new \DateTime($datetime);
		} else {
			throw new \InvalidArgumentException(
				sprintf('Invalid type:%s', (is_object($datetime))
					? get_class($datetime)
					: gettype($datetime)
				)
			);
		}
		$this->format = (isset($format)) ? $format : 'Y-m-d H:i:s';
	}

	/**
	 * 日時をタイムスタンプでセットします。
	 *
	 * @param int タイムスタンプ
	 */
	public function setTimestamp($time)
	{
		$this->datetime->setTimestamp($time);
		return $this;
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
	 * 書式化した日付文字列を返します。
	 *
	 * @return string 書式化した日付文字列
	 */
	public function format($format)
	{
		return $this->datetime->format($format);
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
	 * 指定日時との差を秒数で返します
	 *
	 * @param string | \DateTime 日時
	 * @param bool 負の値を返すかどうか
	 */
	public function diffTime($datetime, $invert = false)
	{
		if (!$datetime instanceof \DateTime) {
			$datetime = new \DateTime($datetime);
		}
		$interval = $this->datetime->diff($datetime);
		$time = ($interval->y * 31536000) +
				($interval->m * 2592000) +
				($interval->d * 86400) +
				($interval->h * 3600) +
				($interval->i * 60) +
				$interval->s;
		if ($invert && $interval->invert) {
			return $time * -1;
		}
		return $time;
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
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return method_exists($this, $offset);
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if (method_exists($this, $offset)) {
			return $this->{$offset}();
		}
		throw new \BadMethodCallException(
			sprintf('The offset "%s" could not defined.', $offset)
		);
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($offset, $value)
	{
		throw new \BadMethodCallException(
			sprintf('The offset "%s" is read only.', $offset)
		);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($offset)
	{
		throw new \BadMethodCallException(
			sprintf('The offset "%s" is read only.', $offset)
		);
	}

}
