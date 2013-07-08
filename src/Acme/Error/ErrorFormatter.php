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

	/* @const string エラーレベル */
	const ERROR   = 'error';
	const WARNING = 'warning';
	const NOTICE  = 'notice';
	const INFO    = 'info';

	/* @var array PHPエラーレベル */
	private static $errorLabels = array(
		E_ERROR             => 'Fatal error',
		E_WARNING           => 'Warning',
		E_NOTICE            => 'Notice',
		E_STRICT            => 'Strict standards',
		E_RECOVERABLE_ERROR => 'Catchable fatal error',
		E_DEPRECATED        => 'Depricated',
		E_USER_ERROR        => 'User Fatal error',
		E_USER_WARNING      => 'User Warning',
		E_USER_NOTICE       => 'User Notice',
		E_USER_DEPRECATED   => 'User Depricated',
	);

	/**
	 * @var TraceFormatterInterface トレースフォーマッタ
	 */
	private $traceFormatter;

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
		return sprintf("%s[%d]: '%s' in %s on line %d",
			(isset(static::$errorLabels[$errno]))
				? static::$errorLabels[$errno]
				: 'Unknown error',
			$errno,
			$errstr,
			$errfile,
			$errline
		);
	}

	/**
	 * PHPエラー定数値をこのクラスのエラーレベル定数に変換して返します。
	 *
	 * @int PHPエラー定数
	 */
	public function convertErrorLevel($errno)
	{
		switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			return self::NOTICE;
		case E_WARNING:
		case E_USER_WARNING:
			return self::WARNING;
		case E_ERROR:
		case E_USER_ERROR:
		case E_RECOVERABLE_ERROR:
			return self::ERROR;
		case E_STRICT:
		case E_DEPRECATED:
		case E_USER_DEPRECATED:
		default:
			return self::INFO;
		}
	}

}
