<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Value;

use Acme\Value\AbstractValue;

/**
 * バイト数
 *
 * @author k.holy74@gmail.com
 */
class Byte implements ValueInterface, \ArrayAccess, \IteratorAggregate
{

	use ValueTrait;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var int
	 */
	protected $decimals;

	/**
	 * @var array バイト単位
	 */
	protected static $units = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');

	/**
	 * __construct()
	 *
	 * @param array プロパティの配列
	 */
	public function __construct($value = null, array $options = array())
	{

		if (!is_string($value)) {
			$value = (string)$value;
		}
		$value = $this->unitToValue($value);
		if ($value === false) {
			throw new \InvalidArgumentException(
				sprintf('Invalid type:%s', (is_object($value))
					? get_class($value)
					: gettype($value)
				)
			);
		}

		if (!isset($properties['decimals'])) {
			$properties['decimals'] = 0;
		}

		$this->initialize($value, $options);
	}

	/**
	 * 数値をバイト単位で整形します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->format($this->decimals);
	}

	/**
	 * 単位付きバイト数をバイト数に変換して返します。
	 * 2GB以上を扱うにはBCMath関数が有効になっている必要があります。
	 * ファイル最大値の指定が解析不能な場合はfalseを返します。
	 *
	 * @param string バイト数または単位付きバイト数(B,KB,MB,GB,TB,PB,EB,ZB,YB)
	 * @return mixed バイト数またはFALSE
	 */
	private function unitToValue($data)
	{
		$pattern = sprintf('/\A(\d+)(%s)*\z/i', implode('|', self::$units));
		if (preg_match($pattern, $data, $matches)) {
			if (isset($matches[2])) {
				return $this->mulAndPow(
					$matches[1],
					(int)array_search($matches[2], self::$units)
				);
			}
			return $matches[1];
		}
		return false;
	}

	/**
	 * バイト数の数値をバイト単位で整形して返します。
	 *
	 * @param int 少数桁数
	 * @return string 単位付きバイト数(B,KB,MB,GB,TB,PB,EB,ZB,YB)
	 */
	public function format($decimals = null)
	{
		if (!isset($decimals)) {
			$decimals = $this->decimals;
		}
		$unit = '';
		$number = $value = $this->value;
		foreach (self::$units as $_unit) {
			$unit = $_unit;
			$number = $value;
			if ($value < 1024) {
				break;
			}
			$value = $value / 1024;
		}
		return number_format($number, $decimals) . $unit;
	}

	private function mulAndPow($num, $pow)
	{
		if (function_exists('gmp_pow')) {
			return gmp_strval(gmp_mul($num, gmp_pow('1024', $pow)));
		}
		if (function_exists('bcpow')) {
			return bcmul($num, bcpow('1024', $pow));
		}
		return $num * pow(1024, $pow);
	}

}
