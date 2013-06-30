<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Renderer;

/**
 * レンダラ用Trait
 *
 * @author k.holy74@gmail.com
 */
trait RendererTrait
{

	/**
	 * 出力データに値を追加します。
	 *
	 * @param string 名前
	 * @param mixed 値
	 */
	public function assign($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * 指定パスのテンプレートを読み込んで配列を展開し、結果を出力します。
	 *
	 * @param string テンプレートファイルのパス
	 * @param array テンプレートに展開する変数の配列
	 */
	public function render($view, array $data)
	{
		echo $this->fetch($view, $data);
	}

}
