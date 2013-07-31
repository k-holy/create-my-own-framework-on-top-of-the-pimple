<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database\Driver\Pdo;

use Acme\Database\Database;
use Acme\Database\StatementInterface;

/**
 * PDOステートメント
 *
 * @author k.holy74@gmail.com
 */
class PdoStatement implements StatementInterface, \IteratorAggregate
{

	private $statement;
	private $fetchMode;
	private $fetchClass;

	/**
	 * コンストラクタ
	 *
	 * @param PDOStatement
	 */
	public function __construct(\PDOStatement $statement)
	{
		$this->fetchMode  = Database::FETCH_ASSOC;
		$this->fetchClass = null;
		$this->statement = $statement;
	}

	/**
	 * プリペアドステートメントを実行します。
	 *
	 * @param array パラメータ
	 * @return bool
	 */
	public function execute(array $parameters = array())
	{
		foreach ($parameters as $name => $value) {
			$this->statement->bindValue(
				(strncmp(':', $name, 1) !== 0) ? sprintf(':%s', $name) : $name,
				$value
			);
		}
		try {
			return $this->statement->execute();
		} catch (\PDOException $e) {
			ob_start();
			$this->statement->debugDumpParams();
			$debug = ob_get_contents();
			ob_end_clean();
			throw new \InvalidArgumentException(
				sprintf('execute prepared statement failed. "%s"', $debug)
			);
		}
	}

	/**
	 * このステートメントのデフォルトのフェッチモードを設定します。
	 *
	 * @param int フェッチモード定数 (Database::FETCH_**)
	 * @param mix フェッチモードのオプション
	 */
	public function setFetchMode($mode, $value = null)
	{
		switch ($mode) {
		case Database::FETCH_ASSOC:
			$this->fetchMode = $mode;
			$this->fetchClass = null;
			$this->statement->setFetchMode(\PDO::FETCH_ASSOC);
			break;
		case Database::FETCH_NUM:
			$this->fetchMode = $mode;
			$this->fetchClass = null;
			$this->statement->setFetchMode(\PDO::FETCH_NUM);
			break;
		case Database::FETCH_OBJECT:
			$this->fetchMode = $mode;
			$this->fetchClass = $value;
			$this->statement->setFetchMode(\PDO::FETCH_CLASS, $value);
			break;
		}
		return $this;
	}

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @return mixed
	 */
	public function fetch()
	{
		return $this->statement->fetch();
	}

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @return array
	 */
	public function fetchAll()
	{
		$rows = array();
		while ($row = $this->fetch()) {
			$rows[] = $row;
		}
		return $rows;
	}

	/**
	 * \IteratorAggregate::getIterator()
	 *
	 * @return \Traversable
	 */
	public function getIterator()
	{
		return $this->statement;
	}

}
