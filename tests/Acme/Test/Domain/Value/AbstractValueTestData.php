<?php
/**
 * Create my own framework on top of the Pimple
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
final class AbstractValueTestData extends AbstractValue implements ValueInterface, \ArrayAccess, \IteratorAggregate
{

	protected $value;
	protected $format;
	protected $timezone;

	public function __construct(\DateTime $value = null, array $options = array())
	{
		if (!isset($options['format'])) {
			$options['format'] = 'Y-m-d H:i:s';
		}
		if (!isset($options['timezone'])) {
			$options['timezone'] = new \DateTimeZone(date_default_timezone_get());
		}
		if ($value !== null) {
			$value = clone $value;
			$value->setTimezone($options['timezone']);
		}
		$this->initialize($value, $options);
	}

	public function __toString()
	{
		return ($this->value !== null) ? $this->value->format($this->format) : null;
	}

	protected function setFormat($format)
	{
		$this->format = $format;
	}

	protected function setTimezone(\DateTimeZone $timezone = null)
	{
		$this->timezone = $timezone;
	}

}