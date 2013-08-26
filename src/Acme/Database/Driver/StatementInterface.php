<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database\Driver;

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
	 * @param array | \Traversable パラメータ
	 */
	public function execute($parameters = null);

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
	 * 結果セットから次の行をオブジェクトで取得して返します。
	 *
	 * @param string クラス名
	 * @param array コンストラクタ引数
	 * @return mixed
	 */
	public function fetchObject($class, $arguments = array());

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @param callable コールバック関数
	 * @return array
	 */
	public function fetchAll($function = null);

}
