<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Form;

/**
 * フォーム要素
 *
 * @author k.holy74@gmail.com
 */
class Element implements ElementInterface, \ArrayAccess
{

	/**
	 * @var string 要素名
	 */
	private $name;

	/**
	 * @var mixed この要素の値
	 */
	private $value;

	/**
	 * @var string この要素のエラー
	 */
	private $error;

	/**
	 * コンストラクタ
	 *
	 * @param string 要素名
	 * @param mixed この要素の値
	 */
	public function __construct($name, $value = null)
	{
		$this->name = $name;
		$this->value($value);
	}

	/**
	 * 要素名を返します。
	 *
	 * @return string 要素名
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * 引数1の場合は要素の値を返します。
	 * 引数2の場合は要素に値をセットして$thisを返します。
	 *
	 * @param string 値
	 * @return mixed 値または $this
	 */
	public function value()
	{
		switch (func_num_args()) {
		case 0:
			return $this->value;
		case 1:
			$value = func_get_arg(0);
			$this->value = $value;
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 要素に値があるかどうかを返します。
	 *
	 * @return bool
	 */
	public function hasValue()
	{
		return isset($this->value);
	}

	/**
	 * 引数1の場合は要素のエラーを返します。
	 * 引数2の場合は要素にエラーをセットして$thisを返します。
	 *
	 * @param string エラー
	 * @return mixed エラーまたは $this
	 */
	public function error()
	{
		switch (func_num_args()) {
		case 0:
			return $this->error;
		case 1:
			$error = func_get_arg(0);
			$this->error = $error;
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 要素にエラーがあるかどうかを返します。
	 *
	 * @return bool
	 */
	public function hasError()
	{
		return isset($this->error);
	}

	/**
	 * 要素の値に指定された値が含まれるかどうかを返します。
	 *
	 * @param mixed value
	 * @return bool
	 */
	public function contains($value)
	{
		if (is_scalar($this->value)) {
			return ($this->value === $value);
		} elseif (is_array($this->value)) {
			return (in_array($value, $this->value, true));
		} else {
			foreach ($this->toArray() as $name => $_value) {
				if ($value === $_value) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 要素の値が指定された値と等しいかどうかを返します。
	 *
	 * @param mixed value
	 * @return bool
	 */
	public function equals($value)
	{
		return ($this->value === $value);
	}

	/**
	 * 要素の値が空かどうかを返します。
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		if (is_null($this->value)) {
			return true;
		} elseif (is_string($this->value)) {
			return (strlen($this->value) === 0);
		} elseif (is_array($this->value) || $this->value instanceof \Countable) {
			return (count($this->value) === 0);
		} elseif (is_object($this->value)) {
			return (count($this->toArray()) === 0);
		}
		return false;
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		if (is_object($this->value)) {
			if (method_exists($this->value, $name) && is_callable(array($this->value, $name))) {
				return $this->value->{$name}();
			}
			if (property_exists($this->value, $name) || method_exists($this->value, '__get')) {
				return $this->value->{$name};
			}
			if (true === ($this->value instanceof \ArrayAccess) && method_exists($this->value, 'offsetGet')) {
				return $this->value->offsetGet($name);
			}
			if (method_exists($this->value, '__call')) {
				return $this->value->{$name}();
			}
		}
		if (is_array($this->value) && array_key_exists($name, $this->value)) {
			return $this->value[$name];
		}
		throw new \RuntimeException(
			sprintf('Undefined property "%s".', $name)
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
		if (is_object($this->value)) {
			if (method_exists($this->value, $name) && is_callable(array($this->value, $name))) {
				$this->value->{$name}($value);
				return;
			}
			if (property_exists($this->value, $name) || method_exists($this->value, '__set')) {
				$this->value->{$name} = $value;
				return;
			}
			if (true === ($this->value instanceof \ArrayAccess) && method_exists($this->value, 'offsetSet')) {
				$this->value->offsetSet($name, $value);
				return;
			}
			if (method_exists($this->value, '__call')) {
				$this->value->{$name}($value);
				return;
			}
		}
		if (is_array($this->value) && array_key_exists($name, $this->value)) {
			$this->value[$name] = $value;
			return;
		}
		throw new \RuntimeException(
			sprintf('Undefined property "%s".', $name)
		);
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name)
	{
		try {
			return (null !== $this->offsetGet($name));
		} catch (\Exception $e) {
		}
		return false;
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($name)
	{
		if ($this->offsetExists($name)) {
			$this->offsetSet($name, null);
		}
	}

	/**
	 * magic getter
	 *
	 * @param string プロパティ名
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * magic setter
	 *
	 * @param string 属性名
	 * @param mixed 属性値
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * magic isset
	 *
	 * @param string 属性名
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * magic unset
	 *
	 * @param string 属性名
	 */
	public function __unset($name)
	{
		$this->offsetUnset($name);
	}

	/**
	 * __toString
	 */
	public function __toString()
	{
		return (string)$this->value;
	}

	/**
	 * 配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray()
	{
		if (is_array($this->value)) {
			return $this->value;
		}
		if (is_object($this->value) && $this->value instanceof \Traversable) {
			$values = array();
			foreach ($this->value as $name => $value) {
				$values[$name] = $value;
			}
			return $values;
		}
		return (array)$this->value;
	}

}
