<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Data;

/**
 * ドメインデータTrait
 *
 * @author k.holy74@gmail.com
 */
trait DataTrait
{

	/**
	 * 引数0の場合は属性値のリストを返します。
	 * 引数1の場合は属性値のリストを初期化します。
	 *
	 * @return mixed 属性値のリスト または self
	 */
	public function attributes()
	{
		switch (func_num_args()) {
		case 0:
			return $this->attributes;
		case 1:
			$attributes = func_get_arg(0);
			foreach ($attributes as $name => $value) {
				$this->offsetSet($name, $value);
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		if (method_exists($this, 'get_' . $name)) {
			return $this->{'get_' . $name}();
		}
		$camelize = $this->camelize($name);
		if (method_exists($this, 'get' . $camelize)) {
			return $this->{'get' . $camelize}();
		}
		if (array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name];
		}
		return null;
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($name, $value)
	{
		if (method_exists($this, 'set_' . $name)) {
			return $this->{'set_' . $name}($value);
		}
		$camelize = $this->camelize($name);
		if (method_exists($this, 'set' . $camelize)) {
			return $this->{'set' . $camelize}($value);
		}
		if (array_key_exists($name, $this->attributes)) {
			$this->attributes[$name] = $value;
		}
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($name)
	{
		if (array_key_exists($name, $this->attributes)) {
			$this->attributes[$name] = null;
		}
	}

	/**
	 * magic getter
	 *
	 * @param string 属性名
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
		return var_export($this->toArray(), true);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->attributes);
	}

	/**
	 * 配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray()
	{
		$values = array();
		foreach (array_keys($this->attributes) as $name) {
			$values[$name] = $this->offsetGet($name);
		}
		return $values;
	}

	/**
	 * @param string  $string
	 * @return string
	 */
	private function camelize($string)
	{
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
	}

}
