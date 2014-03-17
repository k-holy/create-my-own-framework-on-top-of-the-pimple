<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Value;

/**
 * AbstractValue
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractValue
{

	/**
	 * __construct()
	 *
	 * @param mixed 値
	 * @param array オプション
	 */
	public function __construct($value = null, array $options = array())
	{
		$this->initialize($value, $options);
	}

	/**
	 * データを初期化します。
	 *
	 * @param mixed 値
	 * @param array オプション
	 */
	protected function initialize($value = null, array $options = array())
	{
		foreach (array_keys(get_object_vars($this)) as $name) {
			$this->{$name} = null;
			if (array_key_exists($name, $options)) {
				$option = (is_object($options[$name]))
					? clone $options[$name]
					: $options[$name];
				if (method_exists($this, 'set' . $name)) {
					$this->{'set' . $name}($option);
				} else {
					$this->{$name} = $option;
				}
				unset($options[$name]);
			}
		}
		if (count($options) !== 0) {
			throw new \InvalidArgumentException(
				sprintf('Not supported properties [%s]',
					implode(',', array_keys($options))
				)
			);
		}
		$this->value = $value;
		return $this;
	}

	/**
	 * このオブジェクトを配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray()
	{
		$values = array();
		foreach (array_keys(get_object_vars($this)) as $name) {
			$values[$name] = $this->__get($name);
		}
		return $values;
	}

	/**
	 * __isset
	 *
	 * @param mixed
	 * @return bool
	 */
	public function __isset($name)
	{
		return (property_exists($this, $name) && $this->{$name} !== null);
	}

	/**
	 * __get
	 *
	 * @param mixed
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function __get($name)
	{
		if (method_exists($this, 'get' . $name)) {
			return $this->{'get' . $name}();
		}
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		return $this->{$name};
	}

	/**
	 * __clone for clone
	 */
	public function __clone()
	{
		foreach (get_object_vars($this) as $name => $value) {
			if (is_object($value)) {
				$this->{$name} = clone $value;
			}
		}
	}

	/**
	 * __sleep for serialize()
	 *
	 * @return array
	 */
	public function __sleep()
	{
		return array_keys(get_object_vars($this));
	}

	/**
	 * __set_state for var_export()
	 *
	 * @param array
	 * @return object
	 */
	public static function __set_state($options)
	{
		return new static($options);
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return $this->__isset($name);
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function offsetGet($name)
	{
		return $this->__get($name);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator(get_object_vars($this));
	}

	/**
	 * __set
	 *
	 * @param mixed
	 * @param mixed
	 * @throws \LogicException
	 */
	final public function __set($name, $value)
	{
		throw new \LogicException(
			sprintf('The property "%s" could not set.', $name)
		);
	}

	/**
	 * __unset
	 *
	 * @param mixed
	 * @throws \LogicException
	 */
	final public function __unset($name)
	{
		throw new \LogicException(
			sprintf('The property "%s" could not unset.', $name)
		);
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 * @throws \LogicException
	 */
	public function offsetSet($name, $value)
	{
		throw new \LogicException(
			sprintf('The property "%s" could not set.', $name)
		);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 * @throws \LogicException
	 */
	public function offsetUnset($name)
	{
		throw new \LogicException(
			sprintf('The property "%s" could not unset.', $name)
		);
	}

}
