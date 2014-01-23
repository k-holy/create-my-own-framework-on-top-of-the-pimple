<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Test\Domain\Data;

use Acme\Domain\Data\AbstractData;

/**
 * TestData for AbstractData
 *
 * @author k.holy74@gmail.com
 */
final class AbstractDataTestData extends AbstractData
{

	protected $string;
	protected $null;
	protected $boolean;
	protected $datetime;
	protected $dateFormat;

	/**
	 * @param \DateTime
	 */
	protected function setDateTime(\DateTime $datetime)
	{
		$this->datetime = $datetime;
	}

	/**
	 * 日付の出力用書式をセットします。
	 *
	 * @param string
	 */
	protected function setDateFormat($dateFormat)
	{
		$this->dateFormat = $dateFormat;
	}

	/**
	 * @return string
	 */
	public function getDatetimeAsString()
	{
		return (isset($this->datetime)) ? $this->datetime->format($this->dateFormat ?: 'Y-m-d H:i:s') : null;
	}

}
