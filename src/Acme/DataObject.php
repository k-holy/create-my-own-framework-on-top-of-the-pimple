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
		$this->initialize($attributes);
	}

	/**
	 * 属性を初期化します。
	 *
	 * @param array 属性の配列
	 * @return $this
	 */
	public function initialize($attributes = array())
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
		return $this;
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
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return (array_key_exists($name, $this->attributes) && null !== $this->offsetGet($name));
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
	 * magic setter
	 *
	 * @param string 属性名
	 * @param mixed 属性値
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
	 * magic getter
	 *
	 * @param string 属性名
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
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
	 * magic call method
	 *
	 * @param string
	 * @param array
	 */
	public function __call($name, $args)
	{
		if (array_key_exists($name, $this->attributes)) {
			$value = $this->attributes[$name];
			if (is_callable($value)) {
				return call_user_func_array($value, $args);
			}
			return $value;
		}
		throw new \BadMethodCallException(
			sprintf('Undefined Method "%s" called.', $name)
		);
	}

	/**
	 * __toString
	 */
	public function __toString()
	{
		return var_export($this->attributes, true);
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

}
