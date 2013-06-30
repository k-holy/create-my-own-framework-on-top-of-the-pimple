<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Renderer;

/**
 * レンダラインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface RendererInterface
{

	/**
	 * 出力データに値を追加します。
	 *
	 * @param string 名前
	 * @param mixed 値
	 */
	public function assign($name, $value);

	/**
	 * 指定パスのテンプレートを読み込んで配列をローカルスコープの変数に展開し、結果を出力します。
	 *
	 * @param string テンプレートファイルのパス
	 * @param array テンプレートに展開する変数の配列
	 */
	public function render($view, array $data);

	/**
	 * 指定パスのテンプレートを読み込んで配列をローカルスコープの変数に展開します。
	 *
	 * @param string テンプレートファイルのパス
	 * @param array テンプレートに展開する変数の配列
	 * @return string
	 */
	public function fetch($view, array $data);

}
