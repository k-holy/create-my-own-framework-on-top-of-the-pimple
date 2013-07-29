<?php
/**
 * Create my own framework on top of the Pimple
 *
 * @copyright 2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Database;

/**
 * カラムクラス
 *
 * @author k.holy74@gmail.com
 */
class Column
{

	public $name;
	public $type;
	public $maxLength;
	public $scale;
	public $binary;
	public $default;
	public $notNull;
	public $primaryKey;

}
