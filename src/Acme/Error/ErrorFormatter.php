<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Error;

/**
 * エラーフォーマッタ
 *
 * @author k.holy74@gmail.com
 */
class ErrorFormatter
{

	/* @var array PHPエラーレベル */
	private static $errorLevels = array(
		E_ERROR             => 'Fatal error',
		E_WARNING           => 'Warning',
		E_NOTICE            => 'Notice',
		E_STRICT            => 'Strict standards',
		E_RECOVERABLE_ERROR => 'Catchable fatal error',
		E_DEPRECATED        => 'Depricated',
		E_USER_ERROR        => 'Fatal error (by user)',
		E_USER_WARNING      => 'Warning (by user)',
		E_USER_NOTICE       => 'Notice (by user)',
		E_USER_DEPRECATED   => 'Depricated (by user)',
	);

	/**
	 * エラー情報を文字列に整形して返します。
	 *
	 * @param int エラーレベル
	 * @param string エラーメッセージ
	 * @param string エラー発生元ファイル
	 * @param string エラー発生元ファイルの行番号
	 * @return string
	 */
	public function format($errno, $errstr, $errfile, $errline)
	{
		return sprintf("%s: '%s' in %s on line %u",
			(isset(static::$errorLevels[$errno]))
				? static::$errorLevels[$errno]
				: 'Unknown error',
			$errstr,
			$errfile,
			$errline
		);
	}

}
