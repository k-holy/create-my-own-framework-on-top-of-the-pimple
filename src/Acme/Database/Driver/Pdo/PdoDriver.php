<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database\Driver\Pdo;

use Acme\Database\Database;
use Acme\Database\DriverInterface;
use Acme\Database\Column;

/**
 * PDOコネクション
 *
 * @author k_horii@rikcorp.jp
 */
class PdoDriver implements DriverInterface
{

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * コンストラクタ
	 *
	 * @param \PDO
	 */
	public function __construct($pdo = null)
	{
		$this->pdo = null;
		if (isset($pdo)) {
			$this->connect($pdo);
		}
	}

	/**
	 * DBに接続します。
	 *
	 * @param \PDO
	 * @return $this
	 */
	public function connect($pdo)
	{
		if (!($pdo instanceof \PDO)) {
			throw new \InvalidArgumentException(
				sprintf('The argument is not PDO instance. type:%s', gettype($pdo))
			);
		}
		$this->pdo = $pdo;
		return $this;
	}

	/**
	 * DBとの接続を解放します。
	 *
	 * @return bool
	 */
	public function disconnect()
	{
		$this->pdo = null;
		return true;
	}

	/**
	 * SQL実行準備を行い、ステートメントオブジェクトを返します。
	 *
	 * @string SQL
	 * @return PdoStatement
	 */
	public function prepare($query)
	{
		return new PdoStatement($this->pdo->prepare($query));
	}

	/**
	 * SQLを実行し、ステートメントオブジェクトを返します。
	 *
	 * @string SQL
	 * @return PdoStatement
	 */
	public function query($query)
	{
		return new PdoStatement($this->pdo->query($query));
	}

	/**
	 * SQLを実行します。
	 *
	 * @string SQL
	 */
	public function execute($query)
	{
		return $this->pdo->exec($query);
	}

	/**
	 * 最後に発生したエラーを返します。
	 *
	 * @return string
	 */
	public function getLastError()
	{
		$errors = $this->pdo->errorInfo();
		return (isset($errors[2])) ? $errors[2] : null;
	}

	/**
	 * 直近のinsert操作で生成されたIDを返します。
	 *
	 * @return mixed 実行結果
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param string テーブル名
	 * @return array of Column
	 */
	public function getMetaColumns($table)
	{
		$statement = $this->query(sprintf('PRAGMA TABLE_INFO(%s);', $table));
		$statement->setFetchMode(Database::FETCH_NUM);
		$columns = array();
		foreach ($statement as $cols) {
			$column = new Column();
			$column->name = $cols[1];
			if (preg_match("/^(.+)\((\d+),(\d+)/", $cols[2], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
				$scale = is_numeric($matches[3]) ? $matches[3] : -1;
			} elseif (preg_match("/^(.+)\((\d+)/", $cols[2], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
			} else {
				$column->type = $cols[2];
			}
			$column->notNull = (bool)$cols[3];
			$column->primaryKey = (bool)$cols[5];
			$column->binary = (strcasecmp($column->type, 'BLOB') === 0);
			if (!$column->binary && strcmp($cols[4], '') !== 0 && strcasecmp($cols[4], 'NULL') !== 0) {
				$column->default = $cols[4];
			}
			$columns[$cols[1]] = $column;
		}
		return $columns;
	}

}
