<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Form;

use Acme\Form\Element;

/**
 * フォーム
 *
 * @author k.holy74@gmail.com
 */
class Form implements FormInterface, \ArrayAccess, \IteratorAggregate, \Countable
{

	/**
	 * @var string フォーム名
	 */
	private $name;

	/**
	 * @var array フォーム要素の配列
	 */
	private $elements;

	/**
	 * コンストラクタ
	 *
	 * @param string フォーム名
	 * @param array | Traversable 属性の配列
	 */
	public function __construct($name, $attributes = null)
	{
		$this->name = $name;
		$this->elements = array();
		if (isset($attributes)) {
			$this->import($attributes);
		}
	}

	/**
	 * フォームに属性値を取り込みます。
	 *
	 * @param array | Traversable 属性の配列
	 */
	public function import($attributes)
	{
		if (!is_array($attributes) && !($attributes instanceof \Traversable)) {
			throw new \InvalidArgumentException(
				sprintf('The attributes is not Array and not Traversable. type:"%s"',
					(is_object($attributes)) ? get_class($attributes) : gettype($attributes)
				)
			);
		}
		foreach ($attributes as $name => $value) {
			$this->elements[$name] = new Element($name, $value);
		}
		return $this;
	}

	/**
	 * フォームの値を配列にセットして返します。
	 *
	 * @param mixed 返す属性値の名前をキーに持つ配列またはオブジェクト
	 */
	public function export($attributes = null)
	{
		$values = array();
		foreach ($this->elements as $name => $element) {
			if (is_null($attributes) ||
				(is_array($attributes) && array_key_exists($name, $attributes)) ||
				(is_object($attributes) && property_exists($attributes, $name)) ||
				(($attributes instanceof \ArrayAccess) && $attributes->offsetExists($name))
			) {
				$values[$name] = $element->value();
			}
		}
		return $values;
	}

	/**
	 * フォーム名を返します。
	 *
	 * @return string フォーム名
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * 指定された要素のエラーメッセージをセットします。
	 *
	 * @param string 要素名
	 * @param string エラーメッセージ
	 * @return self
	 */
	public function setError($name, $error)
	{
		if (array_key_exists($name, $this->elements)) {
			$this->elements[$name]->error($error);
		}
		return $this;
	}

	/**
	 * 指定された要素にエラーメッセージがセットされているかどうかを返します。
	 *
	 * @param string 要素名
	 * @return bool
	 */
	public function isError($name)
	{
		if (!array_key_exists($name, $this->elements)) {
			throw new \InvalidArgumentException(
				sprintf('The element "%s" is not set in this form.', $name)
			);
		}
		return $this->elements[$name]->hasError();
	}

	/**
	 * エラーメッセージを配列で返します。
	 *
	 * @return array エラーメッセージの配列
	 */
	public function getErrors()
	{
		$errors = array();
		foreach ($this->elements as $name => $element) {
			if ($element->hasError()) {
				$errors[$name] = $element->error();
			}
		}
		return $errors;
	}

	/**
	 * エラーがセットされているかどうかを返します。
	 *
	 * @return bool
	 */
	public function hasError()
	{
		foreach ($this->elements as $name => $element) {
			if ($element->hasError()) {
				return true;
			}
		}
		return false;
	}

	/**
	 * エラーを取り込みます。
	 *
	 * @param array|Traversable エラーの配列
	 * @param string 取り込む際に付与する接頭辞
	 * @return self
	 */
	public function importErrors($errors, $prefix = null)
	{
		foreach ($errors as $name => $error) {
			$this->setError(is_null($prefix) ? $name : sprintf('%s%s', $prefix, $name), $error);
		}
		return $this;
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		if (array_key_exists($name, $this->elements)) {
			return $this->elements[$name];
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
		if (array_key_exists($name, $this->elements)) {
			$this->elements[$name]->value($value);
		} else {
			$this->elements[$name] = new Element($name, $value);
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
		return array_key_exists($name, $this->elements);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($name)
	{
		if (array_key_exists($name, $this->elements)) {
			$this->elements[$name]->value(null);
		}
	}

	/**
	 * magic getter
	 *
	 * @param string 要素名
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * magic setter
	 *
	 * @param string 要素名
	 * @param mixed 要素の値
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * magic isset
	 *
	 * @param string 要素名
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * magic unset
	 *
	 * @param string 要素名
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
		return new \ArrayIterator($this->elements);
	}

	/**
	 * Countable::count()
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->elements);
	}

	/**
	 * 配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray()
	{
		$values = array();
		foreach ($this->elements as $name => $element) {
			$values[$name] = $element->value();
		}
		ksort($values);
		return $values;
	}

}
