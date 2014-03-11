<?php
/**
 * エンティティオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Entity;

/**
 * EntityInterface
 *
 * @author k.holy74@gmail.com
 */
interface EntityInterface
{

	/**
	 * このオブジェクトのIDを返します。
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * このオブジェクトを配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray();

	/**
	 * __isset
	 *
	 * @param mixed
	 * @return bool
	 */
	public function __isset($name);

	/**
	 * __get
	 *
	 * @param mixed
	 */
	public function __get($name);

	/**
	 * __clone for clone
	 */
	public function __clone();

	/**
	 * __sleep for serialize()
	 *
	 * @return array
	 */
	public function __sleep();

	/**
	 * __set_state for var_export()
	 *
	 * @param array
	 * @return object
	 */
	public static function __set_state($properties);

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name);

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	public function offsetGet($name);

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator();

}
