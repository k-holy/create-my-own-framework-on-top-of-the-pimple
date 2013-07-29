<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database\Statement;

/**
 * ステートメントインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface StatementInterface
{

	/**
	 * プリペアドステートメントを実行します。
	 *
	 * @param array パラメータ
	 */
	public function execute(array $parameters = array());

	/**
	 * このステートメントのデフォルトのフェッチモードを設定します。
	 *
	 * @param int フェッチモード定数 (\Acme\Database::FETCH_**)
	 * @param mix フェッチモードのオプション
	 */
	public function setFetchMode($mode, $value = null);

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @return mixed
	 */
	public function fetch();

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @return array
	 */
	public function fetchAll();

}
