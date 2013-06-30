<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Renderer;

/**
 * PHPTALレンダラ
 *
 * @author k.holy74@gmail.com
 */
class PhpTalRenderer implements RendererInterface
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
	 * @var \PHPTAL
	 */
	public $phptal;

	/**
	 * @var array PHPTAL用オプション設定
	 */
	private static $phptal_options = array(
		'outputMode',
		'encoding',
		'templateRepository',
		'phpCodeDestination',
		'phpCodeExtension',
		'cacheLifetime',
		'forceReparse',
	);

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
		$this->config = array_fill_keys(static::$phptal_options, null);
		if (!empty($configurations)) {
			foreach ($configurations as $name => $value) {
				$this->config($name, $value);
			}
		}
		$this->phptal = new \PHPTAL();
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
				case 'templateRepository':
				case 'phpCodeDestination':
					if (!is_string($value) && !is_array($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts string.', $name));
					}
					break;
				case 'encoding':
				case 'phpCodeExtension':
					if (!is_string($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts string.', $name));
					}
					break;
				case 'outputMode':
				case 'cacheLifetime':
					if (!is_int($value) && !ctype_digit($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts bool.', $name));
					}
					$value = (int)$value;
					break;
				case 'forceReparse':
					if (!is_bool($value) && !is_int($value) && !ctype_digit($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts bool.', $name));
					}
					$value = (bool)$value;
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
		foreach ($this->config as $name => $value) {
			if (isset($value) && in_array($name, static::$phptal_options)) {
				$method = 'set' . ucfirst($name);
				if (!method_exists($this->phptal, $method)) {
					throw new \InvalidArgumentException(
						sprintf('The accessor method to "%s" is not defined.', $name));
				}
				switch ($name) {
				case 'phpCodeDestination':
				case 'templateRepository':
					if ('\\' === DIRECTORY_SEPARATOR) {
						$value = (is_array($value))
							? array_map(function($val) {
								return str_replace('\\', '/', $val);
							}, $value)
							: str_replace('\\', '/', $value);
					}
					break;
				}
				$this->phptal->{$method}($value);
			}
		}
		if (strpos($view, '/') === 0) {
			$view = substr($view, 1);
		}
		$data = array_merge($this->data, $data);
		foreach ($data as $name => $value) {
			$this->phptal->set($name, $value);
		}
		return $this->phptal->setTemplate($view)->execute();
	}

}
