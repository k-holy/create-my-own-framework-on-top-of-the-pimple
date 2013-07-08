<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Error;

/**
 * トレースフォーマッタ
 *
 * @author k.holy74@gmail.com
 */
class TraceFormatter implements TraceFormatterInterface
{

	/**
	 * スタックトレースを文字列に整形して返します。
	 *
	 * @param array スタックトレース
	 * @return string
	 */
	public function toString(array $trace)
	{
		$_trace = array();
		foreach ($trace as $info) {
			$_trace[] = $this->format($info);
		}
		return (count($_trace) >= 1)
			? sprintf("\nStack trace:\n%s", implode("\n", $_trace))
			: '';
	}

	/**
	 * トレースを文字列に整形して返します。
	 *
	 * @param array トレース
	 * @return string
	 */
	public function format(array $info)
	{
		return sprintf('%s: %s(%s)',
			$this->formatLocation(
				isset($info['file']) ? $info['file'] : null,
				isset($info['line']) ? $info['line'] : null
			),
			$this->formatFunction(
				isset($info['class']) ? $info['class'] : null,
				isset($info['type']) ? $info['type'] : null,
				isset($info['function']) ? $info['function'] : null
			),
			$this->formatArguments(
				isset($info['args']) ? $info['args'] : null
			)
		);
	}

	/**
	 * トレースのファイル情報を文字列に整形して返します。
	 *
	 * @param string ファイルパス
	 * @param string 行番号
	 * @return string
	 */
	public function formatLocation($file, $line)
	{
		return (isset($file) && isset($line))
			? sprintf('%s(%d)', $file, $line)
			: '[internal function]';
	}

	/**
	 * トレースの関数呼び出し情報を文字列に整形して返します。
	 *
	 * @param string クラス名
	 * @param string 呼び出し種別
	 * @param string 関数名/メソッド名
	 * @return string
	 */
	public function formatFunction($class, $type, $function)
	{
		return sprintf('%s%s%s', $class ?: '', $type ?: '', $function ?: '');
	}

	/**
	 * トレースの関数呼び出しの引数を文字列に整形して返します。
	 *
	 * @param array  引数の配列
	 * @return string
	 */
	public function formatArguments($arguments)
	{
		if (!isset($arguments) || empty($arguments)) {
			return '';
		}
		$self = $this;
		return implode(', ', array_map(function($arg) use ($self) {
			if (is_array($arg)) {
				$vars = array();
				foreach ($arg as $key => $var) {
					$vars[] = sprintf('%s=>%s',
						$self->formatVar($key),
						$self->formatVar($var)
					);
				}
				return sprintf('[%s]', implode(', ', $vars));
			}
			return $self->formatVar($arg);
		}, $arguments));
	}

	/**
	 * 変数の型の文字列表現を返します。
	 *
	 * @param mixed
	 * @return string
	 */
	private function formatVar($var)
	{
		if (is_null($var)) {
			return 'NULL';
		}

		if (is_int($var)) {
			return sprintf('Int(%d)', $var);
		}

		if (is_float($var)) {
			return sprintf('Float(%F)', $var);
		}

		if (is_string($var)) {
			return sprintf("'%s'", $var);
		}

		if (is_bool($var)) {
			return sprintf('Bool(%s)', $var ? 'true' : 'false');
		}

		if (is_array($var)) {
			return 'Array';
		}

		if (is_object($var)) {
			return sprintf('Object(%s)', get_class($var));
		}

		return sprintf('%s', gettype($var));
	}

}
