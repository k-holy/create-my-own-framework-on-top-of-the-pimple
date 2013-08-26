<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database\MetaDataProcessor;

use Acme\Database\Driver\StatementInterface;

/**
 * メタデータプロセッサインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface MetaDataProcessorInterface
{

	/**
	 * テーブル情報を取得するクエリを返します。
	 *
	 * @return string SQL
	 */
	public function metaTablesQuery();

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param \Acme\Database\Driver\StatementInterface ステートメント
	 * @return array of Table
	 */
	public function getMetaTables(StatementInterface $statement);

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	public function metaColumnsQuery($table);

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param \Acme\Database\Driver\StatementInterface ステートメント
	 * @return array of Column
	 */
	public function getMetaColumns(StatementInterface $statement);

}
