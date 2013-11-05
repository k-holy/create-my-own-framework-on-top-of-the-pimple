<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Form;

/**
 * フォームインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface FormInterface
{

	/**
	 * フォームに属性値を取り込みます。
	 *
	 * @param array | Traversable 属性の配列
	 */
	public function import($attributes);

	/**
	 * フォームの値を配列にセットして返します。
	 *
	 * @param mixed 返す属性値の名前をキーに持つ配列またはオブジェクト
	 */
	public function export($attributes = null);

	/**
	 * フォーム名を返します。
	 *
	 * @return string フォーム名
	 */
	public function getName();

	/**
	 * 指定された要素のエラーメッセージをセットします。
	 *
	 * @param string 要素名
	 * @param string エラーメッセージ
	 * @return self
	 */
	public function setError($name, $error);

	/**
	 * 指定された要素にエラーメッセージがセットされているかどうかを返します。
	 *
	 * @param string 要素名
	 * @return bool
	 */
	public function isError($name);

	/**
	 * エラーメッセージを配列で返します。
	 *
	 * @return array エラーメッセージの配列
	 */
	public function getErrors();

	/**
	 * エラーがセットされているかどうかを返します。
	 *
	 * @return bool
	 */
	public function hasError();

	/**
	 * エラーを取り込みます。
	 *
	 * @param array|Traversable エラーの配列
	 * @param string 取り込む際に付与する接頭辞
	 * @return self
	 */
	public function importErrors($errors, $prefix = null);

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name);

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($name, $value);

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name);

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($name);

	/**
	 * magic getter
	 *
	 * @param string 要素名
	 */
	public function __get($name);

	/**
	 * magic setter
	 *
	 * @param string 要素名
	 * @param mixed 要素の値
	 */
	public function __set($name, $value);

	/**
	 * magic isset
	 *
	 * @param string 要素名
	 * @return bool
	 */
	public function __isset($name);

	/**
	 * magic unset
	 *
	 * @param string 要素名
	 */
	public function __unset($name);

	/**
	 * __toString
	 */
	public function __toString();

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator();

	/**
	 * Countable::count()
	 *
	 * @return int
	 */
	public function count();

	/**
	 * 配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray();

}
