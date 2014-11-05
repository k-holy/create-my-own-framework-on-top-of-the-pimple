<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Value;

use Acme\Domain\Value\ValueInterface;

/**
 * バイト数
 *
 * @author k.holy74@gmail.com
 */
class Byte implements ValueInterface, \ArrayAccess
{

	use \Acme\Domain\Value\ValueTrait;

	/**
	 * @var string
	 */
	private $value;

	/**
	 * @var int
	 */
	private $decimals;

	/**
	 * @var array バイト単位
	 */
	private static $units = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');

	/**
	 * __construct()
	 *
	 * @param mixed 値
	 * @param array オプション
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

		if (!isset($options['decimals'])) {
			$options['decimals'] = 0;
		}

		$this->initialize($value, $options);
	}

	/**
	 * このオブジェクトの素の値を返します。
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
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
	 * このオブジェクトの値に指定された値を加算したオブジェクトを返します。
	 *
	 * @param mixed 加算値
	 * @return self 加算したオブジェクト
	 * @throws \RuntimeException GMP関数またはBcMath関数が利用できない場合
	 * @throws \DomainException 減算結果が範囲外になる場合
	 */
	public function add($operand)
	{
		if ($operand instanceof self) {
			$operand = $operand->getValue();
		}
		if (extension_loaded('gmp')) {
			$value = gmp_strval(gmp_add(
				gmp_init($this->value, 10),
				gmp_init($operand, 10)
			), 10);
		} elseif (extension_loaded('bcmath')) {
			$value = bcadd($this->value, $operand);
		} else {
			throw new \RuntimeException(
				'GMP extension or BcMath extension is required for add().'
			);
		}
		try {
			return new self($value, array(
				'decimals' => $this->decimals,
			));
		} catch (\InvalidArgumentException $e) {
			throw new \DomainException(
				sprintf('Invalid operand "%s" for add().', $operand)
			);
		}
		return $this;
	}

	/**
	 * このオブジェクトの値から指定された値を減算したオブジェクトを返します。
	 *
	 * @param mixed 減算値
	 * @return self 減算したオブジェクト
	 * @throws \RuntimeException GMP関数またはBcMath関数が利用できない場合
	 * @throws \DomainException 減算結果が範囲外になる場合
	 */
	public function sub($operand)
	{
		if ($operand instanceof self) {
			$operand = $operand->getValue();
		}
		if (extension_loaded('gmp')) {
			$value = gmp_strval(gmp_sub(
				gmp_init($this->value, 10),
				gmp_init($operand, 10)
			), 10);
		} elseif (extension_loaded('bcmath')) {
			$value = bcsub($this->value, $operand);
		} else {
			throw new \RuntimeException(
				'GMP extension or BcMath extension is required for sub().'
			);
		}
		try {
			return new self($value, array(
				'decimals' => $this->decimals,
			));
		} catch (\InvalidArgumentException $e) {
			throw new \DomainException(
				sprintf('Invalid operand "%s" for sub().', $operand)
			);
		}
		return $this;
	}

	/**
	 * このオブジェクトの値から指定された値を乗算したオブジェクトを返します。
	 *
	 * @param mixed 乗算値
	 * @return self 乗算したオブジェクト
	 * @throws \RuntimeException GMP関数またはBcMath関数が利用できない場合
	 * @throws \DomainException 乗算結果が範囲外になる場合
	 */
	public function mul($operand)
	{
		if ($operand instanceof self) {
			$operand = $operand->getValue();
		}
		if (extension_loaded('gmp')) {
			$value = gmp_strval(gmp_mul(
				gmp_init($this->value, 10),
				gmp_init($operand, 10)
			), 10);
		} elseif (extension_loaded('bcmath')) {
			$value = bcmul($this->value, $operand);
		} else {
			throw new \RuntimeException(
				'GMP extension or BcMath extension is required for mul().'
			);
		}
		try {
			return new self($value, array(
				'decimals' => $this->decimals,
			));
		} catch (\InvalidArgumentException $e) {
			throw new \DomainException(
				sprintf('Invalid operand "%s" for mul().', $operand)
			);
		}
		return $this;
	}

	/**
	 * このオブジェクトの値が指定された値と等しいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値より大きいかどうか
	 */
	public function equalTo($operand)
	{
		try {
			$sub = $this->sub($operand);
		} catch (\DomainException $e) {
			return false;
		}
		return ($sub->getValue() === '0');
	}

	/**
	 * このオブジェクトの値が指定された値と等しいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値より大きいかどうか
	 * @see equalTo()
	 */
	public function eq($operand)
	{
		return $this->equalTo($operand);
	}

	/**
	 * このオブジェクトの値が指定された値より大きいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値より大きいかどうか
	 */
	public function greaterThan($operand)
	{
		try {
			$sub = $this->sub($operand);
		} catch (\DomainException $e) {
			return false;
		}
		return ($sub->getValue() !== '0');
	}

	/**
	 * このオブジェクトの値が指定された値より大きいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値より大きいかどうか
	 * @see greaterThan()
	 */
	public function gt($operand)
	{
		return $this->greaterThan($operand);
	}

	/**
	 * このオブジェクトの値が指定された値と等しい、またはより大きいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値と等しい、またはより大きいかどうか
	 */
	public function greaterThanOrEqualTo($operand)
	{
		try {
			$sub = $this->sub($operand);
		} catch (\DomainException $e) {
			return false;
		}
		return true;
	}

	/**
	 * このオブジェクトの値が指定された値と等しい、またはより大きいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値と等しい、またはより大きいかどうか
	 * @see greaterThanOrEqualTo()
	 */
	public function gte($operand)
	{
		return $this->greaterThanOrEqualTo($operand);
	}

	/**
	 * このオブジェクトの値が指定された値より小さいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値より小さいかどうか
	 */
	public function lessThan($operand)
	{
		return !$this->greaterThanOrEqualTo($operand);
	}

	/**
	 * このオブジェクトの値が指定された値より小さいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値より小さいかどうか
	 * @see lessThan()
	 */
	public function lt($operand)
	{
		return $this->lessThan($operand);
	}

	/**
	 * このオブジェクトの値が指定された値と等しい、またはより小さいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値と等しい、またはより小さいかどうか
	 */
	public function lessThanOrEqualTo($operand)
	{
		return !$this->greaterThan($operand);
	}

	/**
	 * このオブジェクトの値が指定された値と等しい、またはより小さいかどうかを返します。
	 *
	 * @param mixed 比較値
	 * @return bool 比較値と等しい、またはより小さいかどうか
	 * @see lessThanOrEqualTo()
	 */
	public function lte($operand)
	{
		return $this->lessThanOrEqualTo($operand);
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
