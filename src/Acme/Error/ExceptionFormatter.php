<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Error;

/**
 * 例外フォーマッタ
 *
 * @author k.holy74@gmail.com
 */
class ExceptionFormatter
{

	/**
	 * 例外オブジェクトを文字列に整形して返します。
	 *
	 * @param \Exception 例外オブジェクト
	 * @return string
	 */
	public function format(\Exception $e)
	{
		return sprintf("%s[%d]: '%s' in %s on line %u",
			get_class($e),
			$e->getCode(),
			$e->getMessage(),
			$e->getFile(),
			$e->getLine()
		);
	}

}
