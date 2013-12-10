<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme;

/**
 * データオブジェクト
 *
 * @author k.holy74@gmail.com
 */
class DataObject implements \ArrayAccess, \IteratorAggregate
{

	/**
	 * @var array 属性の配列
	 */
	protected $attributes;

	/**
	 * コンストラクタ
	 *
	 * @param array 属性の配列
	 */
	public function __construct($attributes = array())
	{
		if (!is_array($attributes) && !($attributes instanceof \Traversable)) {
			throw new \InvalidArgumentException(
				sprintf('The attributes is not Array and not Traversable. type:"%s"',
					(is_object($attributes)) ? get_class($attributes) : gettype($attributes)
				)
			);
		}
		$this->attributes = array();
		foreach ($attributes as $name => $value) {
			if (method_exists($this, $name)) {
				throw new \InvalidArgumentException(
					sprintf('The property "%s" is already defined as a method.', $name)
				);
			}
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
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name)
	{
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
		$this->attributes[$name] = $value;
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
	 * __isset()
	 *
	 * @param string
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * __get()
	 *
	 * @param string
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * __set()
	 *
	 * @param string
	 * @param mixed
	 */
	public function __set($name, $value)
	{
		if (method_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" is already defined as a method.', $name)
			);
		}
		$this->offsetSet($name, $value);
	}

	/**
	 * __unset()
	 *
	 * @param string
	 */
	public function __unset($name)
	{
		$this->offsetUnset($name);
	}

	/**
	 * __call()
	 *
	 * @param string
	 * @param array
	 */
	public function __call($name, $args)
	{
		if (array_key_exists($name, $this->attributes) && $this->attributes[$name] instanceof \Closure) {
			return call_user_func_array($this->attributes[$name], $args);
		}
		throw new \BadMethodCallException(
			sprintf('Undefined Method "%s" called.', $name)
		);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->toArray());
	}

	/**
	 * __toString
	 */
	public function __toString()
	{
		return var_export($this->toArray(), true);
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
		ksort($values);
		return $values;
	}

}
