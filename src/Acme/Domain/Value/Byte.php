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

		if (!isset($properties['decimals'])) {
			$properties['decimals'] = 0;
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
		if (false === ($operand instanceof Byte)) {
			$operand = new self($operand, array(
				'decimals' => $this->decimals,
			));
		}
		if (function_exists('gmp_add')) {
			$value = gmp_strval(gmp_add(
				gmp_init($this->value, 10),
				gmp_init($operand->getValue(), 10)
			), 10);
		} elseif (function_exists('bcadd')) {
			$value = bcadd($this->value, $operand->getValue());
		} else {
			throw new \RuntimeException('GMP extension and BcMath extension is not loaded.');
		}
		if (isset($value)) {
			try {
				return new self($value, array(
					'decimals' => $this->decimals,
				));
			} catch (\InvalidArgumentException $e) {
				throw new \DomainException(
					sprintf('Invalid added value "%s"', $value)
				);
			}
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
		if (false === ($operand instanceof Byte)) {
			$operand = new self($operand, array(
				'decimals' => $this->decimals,
			));
		}
		if (function_exists('gmp_sub')) {
			$value = gmp_strval(gmp_sub(
				gmp_init($this->value, 10),
				gmp_init($operand->getValue(), 10)
			), 10);
		} elseif (function_exists('bcsub')) {
			$value = bcsub($this->value, $operand->getValue());
		} else {
			throw new \RuntimeException('GMP extension and BcMath extension is not loaded.');
		}
		if (isset($value)) {
			try {
				return new self($value, array(
					'decimals' => $this->decimals,
				));
			} catch (\InvalidArgumentException $e) {
				throw new \DomainException(
					sprintf('Invalid substracted value "%s"', $value)
				);
			}
		}
		return $this;
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
