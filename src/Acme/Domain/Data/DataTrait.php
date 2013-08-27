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
	 * プロパティを初期化します。
	 *
	 * @param array プロパティ
	 * @return self
	 */
	public function initialize($attributes = array())
	{
		foreach ($attributes as $name => $value) {
			$this->offsetSet($name, $value);
		}
		return $this;
	}

	/**
	 * magic setter
	 *
	 * @param string プロパティ名
	 * @param mixed プロパティ値
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
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
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return (array_key_exists($offset, $this->attributes) && null !== $this->offsetGet($offset));
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if (method_exists($this, 'get_' . $offset)) {
			return $this->{'get_' . $offset}();
		} elseif (array_key_exists($offset, $this->attributes)) {
			return $this->attributes[$offset];
		}
		return null;
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($offset, $value)
	{
		if (method_exists($this, 'set_' . $offset)) {
			$this->{'set_' . $offset}($value);
		} elseif (array_key_exists($offset, $this->attributes)) {
			$this->attributes[$offset] = $value;
		}
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($offset)
	{
		if ($this->offsetExists($offset)) {
			$this->offsetSet($offset, null);
		}
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
