<?php
/**
 * セキュリティ
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Security;

/**
 * パスワード処理クラス
 *
 * @author k.holy74@gmail.com
 */
class PasswordProcessor implements PasswordProcessorInterface
{

	/**
	 * @var array 設定値
	 */
	private $config;

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
		$this->config = array();
		$this->config['algorithm'           ] = 'sha256';
		$this->config['stretchingCount'     ] = 0;
		$this->config['saltLength'          ] = 64;
		$this->config['saltChars'           ] = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$this->config['randomPasswordChars' ] = 10;
		$this->config['randomPasswordLength'] = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!#%&+-./:=?[]_';
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
				case 'algorithm':
				case 'saltChars':
				case 'randomPasswordChars':
					if (!is_string($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts string.', $name));
					}
					break;
				case 'stretchingCount':
				case 'saltLength':
				case 'randomPasswordLength':
					if (!is_int($value) && !ctype_digit($value)) {
						throw new \InvalidArgumentException(
							sprintf('The config parameter "%s" only accepts numeric.', $name));
					}
					$value = intval($value);
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
	 * パスワードを非可逆ハッシュ化して返します。
	 *
	 * @param string パスワード
	 * @param string ハッシュソルト
	 * @return string パスワードハッシュ
	 */
	public function encode($password, $hashSalt = null)
	{
		if (!isset($hashSalt)) {
			$hashSalt = $this->createHashSalt();
		}

		$stretchingCount = $this->config('stretchingCount');
		$algorithm = $this->config('algorithm');

		$supportedAlgos = array();
		$supportedAlgos += hash_algos();
		if (!in_array($algorithm, $supportedAlgos)) {
			throw new \RuntimeException(
				sprintf('The algorithm "%s" is not support.', $algorithm)
			);
		}

		$encoded = $password;
		for ($i = 0; $i < $stretchingCount; $i++) {
			$encoded = hash($algorithm, $encoded . $password . $hashSalt, false);
		}

		return $encoded;
	}

	/**
	 * ランダムパスワードを生成します。
	 *
	 * @param string パスワードに利用する文字
	 * @param int パスワードの桁数
	 * @return string ランダムパスワード
	 */
	public function createRandomPassword($length = null, $chars = null)
	{
		if (is_null($length)) {
			$length = $this->config('randomPasswordLength');
		}

		if (empty($length)) {
			throw new \RuntimeException('Unspecified length for randomPassword().');
		}

		if (is_null($chars)) {
			$chars = $this->config('randomPasswordChars') ?: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		}

		if (strlen($chars) === 0) {
			throw new \RuntimeException('Unspecified characters for randomPassword().');
		}

		return self::createRandomString($length, $chars);
	}

	/**
	 * ハッシュソルトを生成します。
	 *
	 * @param string ソルトに利用する文字
	 * @param int ソルトの桁数
	 * @return string ソルト文字列
	 */
	public function createHashSalt($length = null, $chars = null)
	{
		if (is_null($length)) {
			$length = $this->config('saltLength');
		}

		if (empty($length)) {
			throw new \RuntimeException('Unspecified length for randomPassword().');
		}

		if (is_null($chars)) {
			$chars = $this->config('saltChars') ?: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		}

		if (strlen($chars) === 0) {
			throw new \RuntimeException('Unspecified characters for randomPassword().');
		}

		return self::createRandomString($length, $chars);
	}

	/**
	 * オブジェクトの文字列表現を返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return print_r(
			array(
				'class' => get_class($this),
				'config' => $this->config,
			),
			true
		);
	}

	private function createRandomString($length, $chars)
	{
		$string = '';
		$max = strlen($chars) - 1;
		for ($i = 0; $i < $length; $i++) {
			$string .= $chars[mt_rand(0, $max)];
		}
		return $string;
	}

}
