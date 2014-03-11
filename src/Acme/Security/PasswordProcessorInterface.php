<?php
/**
 * セキュリティ
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Security;

/**
 * パスワード処理インタフェース
 *
 * @author k.holy74@gmail.com
 */
interface PasswordProcessorInterface
{

	/**
	 * パスワードを非可逆ハッシュ化して返します。
	 *
	 * @param string パスワード
	 * @param string ハッシュソルト
	 * @return string パスワードハッシュ
	 */
	public function encode($password, $hashSalt = null);

	/**
	 * ランダムパスワードを生成します。
	 *
	 * @param string パスワードに利用する文字
	 * @param int パスワードの桁数
	 * @return string ランダムパスワード
	 */
	public function createRandomPassword($length = null, $chars = null);

	/**
	 * ハッシュソルトを生成します。
	 *
	 * @param string ソルトに利用する文字
	 * @param int ソルトの桁数
	 * @return string ソルト文字列
	 */
	public function createHashSalt($length = null, $chars = null);

	/**
	 * オブジェクトの文字列表現を返します。
	 *
	 * @return string
	 */
	public function __toString();

}
