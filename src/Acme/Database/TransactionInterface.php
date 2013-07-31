<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database;

/**
 * トランザクションインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface TransactionInterface
{

	/**
	 * トランザクションを開始します。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function begin();

	/**
	 * トランザクションをコミットします。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function commit();

	/**
	 * トランザクションをロールバックします。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function rollback();

}
