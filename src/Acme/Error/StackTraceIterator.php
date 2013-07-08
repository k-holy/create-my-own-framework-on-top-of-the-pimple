<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Error;

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
	 * @var TraceFormatterInterface トレースフォーマッタ
	 */
	private $formatter;

	/**
	 * @var int 現在のイテレーション位置
	 */
	private $position;

	/**
	 * コンストラクタ
	 *
	 * @param TraceFormatterInterface トレースフォーマッタ
	 */
	public function __construct(TraceFormatterInterface $formatter)
	{
		$this->formatter = $formatter;
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
		$info = $this->trace[$this->position];
		return array(
			'index' => $this->position,
			'location' => $this->formatter->formatLocation(
				isset($info['file']) ? $info['file'] : null,
				isset($info['line']) ? $info['line'] : null
			),
			'function' => $this->formatter->formatFunction(
				isset($info['class']) ? $info['class'] : null,
				isset($info['type']) ? $info['type'] : null,
				isset($info['function']) ? $info['function'] : null
			),
			'argument' => $this->formatter->formatArguments(
				isset($info['args']) ? $info['args'] : null
			),
		);
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
		foreach ($this->trace as $index => $info) {
			$trace[] = sprintf('#%d %s', $index, $this->formatter->format($info));
		}
		return implode("\n", $trace);
	}

}
