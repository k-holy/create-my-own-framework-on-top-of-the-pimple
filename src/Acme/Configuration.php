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
class Configuration implements \ArrayAccess, \IteratorAggregate, \Countable
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
		$this->attributes = (!empty($attributes)) ? $this->import($attributes) : array();
		return $this;
	}

	/**
	 * 属性値を配列から再帰的にセットします。
	 * 要素が配列またはTraversable実装オブジェクトの場合、
	 * ラッピングすることで配列アクセスとプロパティアクセスを提供します。
	 *
	 * @param array 属性の配列
	 * @return array
	 */
	public function import($attributes)
	{
		foreach ($attributes as $name => $value) {
			$attributes[$name] = (is_array($value) || $value instanceof \Traversable)
				? new static($value)
				: $value
			;
		}
		return $attributes;
	}

	/**
	 * 属性名および初期値をセットします。
	 *
	 * @param string 属性名
	 * @param mixed 初期値
	 * @return $this
	 */
	public function define($name, $value = null)
	{
		if ($this->defined($name)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" already exists.', $name));
		}
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * 属性が定義されているかどうかを返します。
	 *
	 * @param string 属性名
	 * @return boolean 属性が定義されているかどうか
	 */
	public function defined($name)
	{
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * 引数なしの場合は全ての属性を配列で返します。
	 * 引数ありの場合は全ての属性を引数の配列からセットして$thisを返します。
	 *
	 * @param array 属性の配列
	 * @return mixed 属性の配列 または $this
	 */
	public function attributes()
	{
		switch (func_num_args()) {
		case 0:
			return $this->attributes;
		case 1:
			$attributes = func_get_arg(0);
			if (!is_array($attributes) && !($attributes instanceof \Traversable)) {
				throw new \InvalidArgumentException(
					'The attributes is not Array and not Traversable.');
			}
			foreach ($attributes as $name => $value) {
				$this->set($name, $value);
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 属性名を配列で返します。
	 *
	 * @return array 属性名の配列
	 */
	public function keys()
	{
		return array_keys($this->attributes);
	}

	/**
	 * 属性値を配列で返します。
	 *
	 * @return array 属性値の配列
	 */
	public function values()
	{
		return array_values($this->attributes);
	}

	/**
	 * 指定された属性の値をセットします。
	 *
	 * @param string 属性名
	 * @param mixed 属性値
	 */
	public function set($name, $value)
	{
		if (!$this->defined($name)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $name));
		}
		$this->attributes[$name] = $value;
	}

	/**
	 * 指定された属性の値を返します。
	 *
	 * @param string 属性名
	 * @return mixed 属性値
	 */
	public function get($name)
	{
		if (!$this->defined($name)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $name));
		}
		return (is_callable($this->attributes[$name]))
			? $this->attributes[$name]($this)
			: $this->attributes[$name];
	}

	/**
	 * magic setter
	 *
	 * @param string 属性名
	 * @param mixed 属性値
	 */
	public function __set($name, $value)
	{
		return $this->set($name, $value);
	}

	/**
	 * magic getter
	 *
	 * @param string 属性名
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

	/**
	 * magic call method
	 *
	 * @param string
	 * @param array
	 */
	public function __call($name, $args)
	{
		if ($this->defined($name)) {
			$value = $this->get($name);
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
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return $this->defined($offset);
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($offset, $value)
	{
		$this->set($offset, $value);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($offset)
	{
		if ($this->defined($offset)) {
			$this->set($offset, null);
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

	/**
	 * Countable::count()
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->attributes);
	}

}
