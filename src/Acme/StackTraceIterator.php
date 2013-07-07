<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme;

/**
 * スタックトレースイテレータ
 *
 * @author k.holy74@gmail.com
 */
class StackTraceIterator implements \Iterator, \Countable
{

	/**
	 * @var array スタックトレース
	 */
	private $trace;

	/**
	 * @var int 現在のイテレーション位置
	 */
	private $position;

	/**
	 * コンストラクタ
	 *
	 * @param array スタックトレース
	 */
	public function __construct(array $trace = array())
	{
		$this->initialize($trace);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param array スタックトレース
	 * @return $this
	 */
	public function initialize(array $trace = array())
	{
		$this->position = 0;
		if (!empty($trace)) {
			$this->trace = $trace;
		}
		return $this;
	}

	/**
	 * Iterator::rewind()
	 */
	public function rewind()
	{
		$this->position = 0;
	}

	/**
	 * Iterator::current()
	 *
	 * @return array
	 */
	public function current()
	{
		return $this->format($this->trace[$this->position], $this->position);
	}

	/**
	 * Iterator::key()
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Iterator::next()
	 */
	public function next()
	{
		$this->position++;
	}

	/**
	 * Iterator::valid()
	 *
	 * @return bool
	 */
	public function valid()
	{
		return isset($this->trace[$this->position]);
	}

	/**
	 * Countable::count()
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->trace);
	}

	/**
	 * スタックトレースを文字列に整形して返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		$trace = array();
		foreach ($this as $current) {
			$trace[] = sprintf('#%d %s: %s(%s)',
				$current['index'],
				$current['location'],
				$current['function'],
				$current['argument']
			);
		}
		return implode("\n", $trace);
	}

	/**
	 * スタックトレースの配列を整形して返します。
	 *
	 * @param array 要素
	 * @param int インデックス
	 * @return array
	 */
	public function format(array $current, $index)
	{
		$location = (isset($current['file']) && isset($current['line']))
			? sprintf('%s(%d)', $current['file'], $current['line'])
			: '[internal function]';

		$function = sprintf('%s%s%s',
			(isset($current['class'   ])) ? $current['class'   ] : '',
			(isset($current['type'    ])) ? $current['type'    ] : '',
			(isset($current['function'])) ? $current['function'] : ''
		);

		$argument = (isset($current['args']) && !empty($current['args']))
			? $this->formatArgs($current['args'])
			: '';

		return array(
			'index'    => $index,
			'location' => $location,
			'function' => $function,
			'argument' => $argument,
		);
	}

	/**
	 * スタックトレースのargs要素を文字列に整形して返します。
	 *
	 * @param array args要素
	 * @return string
	 */
	private function formatArgs($args)
	{
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
		}, $args));
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
