<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Data;

use Acme\DateTime;

/**
 * Commentクラス
 *
 * @author k.holy74@gmail.com
 */
class Comment implements \ArrayAccess, \IteratorAggregate
{
	use DataTrait;

	private $timezone;

	private $attributes = [
		'author'    => null,
		'comment'   => null,
		'posted_at' => null,
	];

	public function __construct($options = null)
	{
		if (isset($options['timezone'])) {
			$this->timezone = $options['timezone'];
		}
	}

	/**
	 * setter for posted_at
	 *
	 * @param mixed
	 */
	public function set_posted_at($value)
	{
		if (false === ($value instanceof DateTime)) {
			$datetime = new DateTime($value, 'Y-m-d H:i:s');
		}
		if (isset($this->timezone)) {
			$datetime->setTimeZone($this->timezone);
		}
		$this->attributes['posted_at'] = $datetime->timestamp();
	}

	/**
	 * getter for posted_at
	 *
	 * @return \Acme\DateTime
	 */
	public function get_posted_at()
	{
		if (isset($this->attributes['posted_at'])) {
			$datetime = new DateTime($this->attributes['posted_at'], 'Y-m-d H:i:s');
			if (isset($this->timezone)) {
				$datetime->setTimeZone($this->timezone);
			}
			return $datetime;
		}
		return null;
	}

}
