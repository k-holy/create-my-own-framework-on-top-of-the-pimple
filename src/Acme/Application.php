<?php
/**
 * Create my own framework on top of the Pimple
 *
 * [Pimple](http://pimple.sensiolabs.org/)
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme;

/**
 * アプリケーションクラス
 *
 * @author k.holy74@gmail.com
 */
class Application extends \Pimple
{

	/**
	 * @var array of callable イベントハンドラ関数の配列
	 */
	private $handlers;

	/**
	 * コンストラクタ
	 *
	 * @param array 設定値/オブジェクトの配列
	 */
	public function __construct(array $values = array())
	{
		parent::__construct($values);
		$this->handlers = array();
	}

	/**
	 * __get()
	 *
	 * @param string
	 * @return mixed
	 */
	public function __get($name)
	{
		return parent::offsetGet($name);
	}

	/**
	 * __set()
	 *
	 * @param string
	 * @param mixed
	 */
	public function __set($name, $value)
	{
		parent::offsetSet($name, $value);
	}

	/**
	 * __call()
	 *
	 * @param string
	 * @param array
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		if (parent::offsetExists($name)) {
			$value = parent::offsetGet($name);
			if (is_callable($value)) {
				return call_user_func_array($value, $args);
			}
			return $value;
		}
		if (array_key_exists($name, $this->handlers)) {
			switch (count($args)) {
				case 0:
					return $this->execute($name);
				case 1:
					return $this->execute($name, $args[0]);
				case 2:
					return $this->execute($name, $args[0], $args[1]);
				case 3:
					return $this->execute($name, $args[0], $args[1], $args[2]);
				case 4:
					return $this->execute($name, $args[0], $args[1], $args[2], $args[3]);
			}
		}
		throw new \BadMethodCallException(
			sprintf('Undefined Method "%s" called.', $name)
		);
	}

	/**
	 * イベントを登録します。
	 *
	 * @param string イベント名
	 * @param array of callable ハンドラ関数のリスト
	 * @return $this
	 */
	public function registerEvent($event, $handlers = null)
	{
		if (array_key_exists($event, $this->handlers)) {
			throw new \InvalidArgumentException(
				sprintf('The event "%s" is already defined.', $event)
			);
		}
		$this->handlers[$event] = array();
		if (isset($handlers)) {
			if (!is_array($handlers)) {
				throw new \InvalidArgumentException(
					sprintf('The event "%s" handlers is not array. type:%s', $name, gettype($handlers))
				);
			}
			foreach ($handlers as $handler) {
				$this->addHandler($event, $handler);
			}
		}
		return $this;
	}

	/**
	 * イベントハンドラ関数を追加します。
	 *
	 * @param string イベント名
	 * @param callable ハンドラ関数
	 * @return $this
	 */
	public function addHandler($event, callable $handler)
	{
		if (!array_key_exists($event, $this->handlers)) {
			throw new \InvalidArgumentException(
				sprintf('The event "%s" is not defined.', $event)
			);
		}
		$this->handlers[$event][] = $handler;
		return $this;
	}

	/**
	 * イベントハンドラを実行します。
	 *
	 * @param string イベント名
	 * @return $this
	 */
	public function execute($event)
	{
		if (!array_key_exists($event, $this->handlers)) {
			throw new \InvalidArgumentException(
				sprintf('The event "%s" is not defined.', $event)
			);
		}
		$args = func_get_args();
		$args[0] = $this;
		foreach ($this->handlers[$event] as $handler) {
			$result = call_user_func_array($handler, $args);
		}
		if (isset($result)) {
			return $result;
		}
		return $this;
	}

}
