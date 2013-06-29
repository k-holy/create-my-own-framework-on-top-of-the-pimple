<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme;

/**
 * レンダラクラス
 *
 * @author k.holy74@gmail.com
 */
class Renderer
{

	/**
	 * @var array 設定値
	 */
	private $config;

	/**
	 * @var array 出力データ
	 */
	private $data;

	/**
	 * コンストラクタ
	 *
	 * @param array | ArrayAccess 設定オプション
	 */
	public function __construct($configurations = array())
	{
		$this->initialize($configurations);
	}

	/**
	 * オブジェクトを初期化します。
	 *
	 * @param array | ArrayAccess 設定オプション
	 */
	public function initialize($configurations = array())
	{
		$this->data = array();
		$this->config = array(
			'template_dir' => null,
		);
		if (!empty($configurations)) {
			foreach ($configurations as $name => $value) {
				$this->config($name, $value);
			}
		}
		return $this;
	}

	/**
	 * 引数1の場合は指定された設定の値を返します。
	 * 引数2の場合は指定された設置の値をセットして$thisを返します。
	 *
	 * @param string 設定名
	 * @return mixed 設定値 または $this
	 */
	public function config($name)
	{
		switch (func_num_args()) {
		case 1:
			return $this->config[$name];
		case 2:
			$value = func_get_arg(1);
			if (isset($value)) {
				switch ($name) {
				case 'template_dir':
					if (!is_string($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts string.', $name));
					}
					break;
				default:
					throw new \InvalidArgumentException(
						sprintf('The config parameter "%s" is not defined.', $name)
					);
				}
				$this->config[$name] = $value;
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * 出力データに値を追加します。
	 *
	 * @param string 名前
	 * @param mixed 値
	 * @return string
	 */
	public function assign($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * 指定パスのテンプレートを読み込んで配列をローカルスコープの変数に展開し、結果を出力します。
	 *
	 * @param string テンプレートファイルのパス
	 * @param array テンプレートに展開する変数の配列
	 */
	public function render($view, array $data)
	{
		echo $this->fetch($view, $data);
	}

	/**
	 * 指定パスのテンプレートを読み込んで配列をローカルスコープの変数に展開します。
	 *
	 * @param string テンプレートファイルのパス
	 * @param array テンプレートに展開する変数の配列
	 * @return string
	 */
	public function fetch($view, array $data)
	{
		$dir = $this->config('template_dir');
		if (isset($dir)) {
			$dir = rtrim($dir, '/');
		}
		$template = (isset($dir)) ? $dir . DIRECTORY_SEPARATOR . $view : $view;
		if ('\\' === DIRECTORY_SEPARATOR) {
			$template = str_replace('\\', '/', $template);
		}
		if (false !== realpath($template)) {
			ob_start();
			$data = array_merge($this->data, $data);
			extract($data);
			include $template;
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}
	}

}
