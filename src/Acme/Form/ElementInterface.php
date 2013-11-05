<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Form;

/**
 * フォーム要素
 *
 * @author k.holy74@gmail.com
 */
interface ElementInterface
{

	/**
	 * 要素名を返します。
	 *
	 * @return string 要素名
	 */
	public function getName();

	/**
	 * 引数1の場合は要素の値を返します。
	 * 引数2の場合は要素に値をセットして$thisを返します。
	 *
	 * @param string 値
	 * @return mixed 値または $this
	 */
	public function value();

	/**
	 * 要素に値があるかどうかを返します。
	 *
	 * @return bool
	 */
	public function hasValue();

	/**
	 * 引数1の場合は要素のエラーを返します。
	 * 引数2の場合は要素にエラーをセットして$thisを返します。
	 *
	 * @param string エラー
	 * @return mixed エラーまたは $this
	 */
	public function error();

	/**
	 * 要素にエラーがあるかどうかを返します。
	 *
	 * @return bool
	 */
	public function hasError();

	/**
	 * 要素の値に指定された値が含まれるかどうかを返します。
	 *
	 * @param mixed value
	 * @return bool
	 */
	public function contains($value);

	/**
	 * 要素の値が指定された値と等しいかどうかを返します。
	 *
	 * @param mixed value
	 * @return bool
	 */
	public function equals($value);

	/**
	 * magic getter
	 *
	 * @param string プロパティ名
	 */
	public function __get($name);

	/**
	 * magic setter
	 *
	 * @param string 属性名
	 * @param mixed 属性値
	 */
	public function __set($name, $value);

	/**
	 * magic isset
	 *
	 * @param string 属性名
	 * @return bool
	 */
	public function __isset($name);

	/**
	 * magic unset
	 *
	 * @param string 属性名
	 */
	public function __unset($name);

	/**
	 * __toString
	 */
	public function __toString();

}
