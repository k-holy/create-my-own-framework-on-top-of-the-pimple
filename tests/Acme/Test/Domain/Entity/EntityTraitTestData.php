<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Entity;

use Acme\Domain\Entity\EntityInterface;
use Acme\Domain\Entity\EntityTrait;

/**
 * TestData for EntityTrait
 *
 * @author k.holy74@gmail.com
 */
final class EntityTraitTestData implements EntityInterface, \ArrayAccess, \IteratorAggregate
{
	use EntityTrait;

	private $id;
	private $string;
	private $null;
	private $boolean;
	private $datetime;
	private $dateFormat;

	public function getId()
	{
		return $this->id;
	}

	private function setDateTime(\DateTime $datetime)
	{
		$this->datetime = $datetime;
	}

	private function setDateFormat($dateFormat)
	{
		$this->dateFormat = $dateFormat;
	}

	public function getDatetimeAsString()
	{
		return (isset($this->datetime)) ? $this->datetime->format($this->dateFormat ?: 'Y-m-d H:i:s') : null;
	}

}
