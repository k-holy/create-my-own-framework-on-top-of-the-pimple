<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme;

/**
 * 設定クラス
 *
 * @author k.holy74@gmail.com
 */
class Configuration implements \ArrayAccess, \IteratorAggregate
{

	/**
	 * @var array 属性の配列
	 */
	private $attributes;

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
		$this->attributes = (!empty($attributes)) ? $this->import($attributes) : array();
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->attributes);
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if (!array_key_exists($offset, $this->attributes)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset));
		}
		return (is_callable($this->attributes[$offset]))
			? $this->attributes[$offset]($this)
			: $this->attributes[$offset];
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($offset, $value)
	{
		if (!array_key_exists($offset, $this->attributes)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset));
		}
		$this->attributes[$offset] = $value;
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($offset)
	{
		if (array_key_exists($offset, $this->attributes)) {
			$this->attributes[$offset] = null;
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
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->attributes);
	}

	/**
	 * 属性値を配列から再帰的にセットします。
	 * 要素が配列またはTraversable実装オブジェクトの場合、
	 * ラッピングすることで配列アクセスとプロパティアクセスを提供します。
	 *
	 * @param array 属性の配列
	 * @return array
	 */
	private function import($attributes)
	{
		foreach ($attributes as $name => $value) {
			if (method_exists($this, $name)) {
				throw new \InvalidArgumentException(
					sprintf('The attribute "%s" is already defined as a method.', $name)
				);
			}
			$attributes[$name] = (is_array($value) || $value instanceof \Traversable)
				? new static($value)
				: $value
			;
		}
		return $attributes;
	}

}
