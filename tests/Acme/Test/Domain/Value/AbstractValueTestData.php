<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Value;

use Acme\Domain\Value\AbstractValue;
use Acme\Domain\Value\ValueInterface;

/**
 * TestData for AbstractValue
 *
 * @author k.holy74@gmail.com
 */
final class AbstractValueTestData extends AbstractValue implements ValueInterface, \ArrayAccess
{

	protected $value;
	protected $format;
	protected $timezone;

	public function __construct(\DateTime $value = null, array $options = array())
	{
		if ($value === null) {
			$value = new \DateTime();
		}
		if (!isset($options['timezone'])) {
			$options['timezone'] = new \DateTimeZone(date_default_timezone_get());
		}
		$value->setTimezone($options['timezone']);
		if (!isset($options['format'])) {
			$options['format'] = 'Y-m-d H:i:s';
		}
		$this->initialize($value, $options);
	}

	public function getValue()
	{
		return $this->value;
	}

	public function __toString()
	{
		return $this->value->format($this->format);
	}

	protected function setFormat($format)
	{
		$this->format = $format;
	}

	protected function setTimezone(\DateTimeZone $timezone)
	{
		$this->timezone = $timezone;
	}

}
