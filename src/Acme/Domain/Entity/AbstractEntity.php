<?php
/**
 * エンティティオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Entity;

/**
 * AbstractEntity
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractEntity
{

	/**
	 * __construct()
	 *
	 * @param array プロパティの配列
	 */
	public function __construct(array $properties = array())
	{
		$this->initialize($properties);
	}

	/**
	 * データを初期化します。
	 *
	 * @param array プロパティの配列
	 */
	protected function initialize(array $properties = array())
	{
		foreach (array_keys(get_object_vars($this)) as $name) {
			$this->{$name} = null;
			if (array_key_exists($name, $properties)) {
				$value = (is_object($properties[$name]))
					? clone $properties[$name]
					: $properties[$name];
				if (method_exists($this, 'set' . $name)) {
					$this->{'set' . $name}($value);
				} else {
					$this->{$name} = $value;
				}
				unset($properties[$name]);
			}
		}
		if (count($properties) !== 0) {
			throw new \InvalidArgumentException(
				sprintf('Not supported properties [%s]',
					implode(',', array_keys($properties))
				)
			);
		}
		return $this;
	}

	/**
	 * データを複製します。
	 * プロパティの配列が指定されていれば、値を書き換えます。
	 *
	 * @param array | \ArrayAccess プロパティの配列
	 * @return Acme\Domain\Entity\EntityInterface
	 */
	public function copy($entity = array())
	{
		$isArray = is_array($entity);
		$isArrayAccess = ($entity instanceof \ArrayAccess);
		if (!$isArray && !$isArrayAccess) {
			throw new \InvalidArgumentException(
				sprintf('The entity is not an Array and not instanceof ArrayAccess. type:[%s]',
					(is_object($entity)) ? get_class($entity) : gettype($entity)
				)
			);
		}
		$properties = array();
		foreach (array_keys(get_object_vars($this)) as $name) {
			if (($isArray && array_key_exists($name, $entity)) ||
				($isArrayAccess && $entity->offsetExists($name))
			) {
				$properties[$name] = $entity[$name];
			} elseif ($this->{$name} !== null) {
				$properties[$name] = $this->{$name};
			}
		}
		return new static($properties);
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
		return property_exists($this, $name);
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
	public static function __set_state($properties)
	{
		return new static($properties);
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
