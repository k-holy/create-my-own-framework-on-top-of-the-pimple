<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Entity;

use Acme\Domain\Entity\AbstractEntity;
use Acme\Domain\Entity\EntityInterface;

/**
 * TestData for AbstractEntity
 *
 * @author k.holy74@gmail.com
 */
final class AbstractEntityTestData extends AbstractEntity implements EntityInterface, \ArrayAccess, \IteratorAggregate
{

	protected $id;
	protected $string;
	protected $null;
	protected $boolean;
	protected $datetime;
	protected $dateFormat;

	protected function setDateTime(\DateTime $datetime)
	{
		$this->datetime = $datetime;
	}

	protected function setDateFormat($dateFormat)
	{
		$this->dateFormat = $dateFormat;
	}

	public function getDatetimeAsString()
	{
		return (isset($this->datetime)) ? $this->datetime->format($this->dateFormat ?: 'Y-m-d H:i:s') : null;
	}

}
