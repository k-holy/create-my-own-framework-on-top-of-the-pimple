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
 * ファイルパス
 *
 * @author k.holy74@gmail.com
 */
class Filepath implements ValueInterface, \ArrayAccess
{

	use \Acme\Domain\Value\ValueTrait;

	/** ディレクトリ区切り文字 **/
	const SEPARATOR = '/';

	/**
	 * @var string
	 */
	private $value;

	/**
	 * @var string
	 */
	private $dirname;

	/**
	 * @var string
	 */
	private $basename;

	/**
	 * @var string
	 */
	private $extension;

	/**
	 * @var bool
	 */
	private $isDir;

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

		// ディレクトリ区切り文字を統一し、先頭のディレクトリ区切り文字を除去
		$value = $this->regularizeValue($value);

		$dirpos = strrpos($value, self::SEPARATOR);

		// 末尾がディレクトリ区切り文字の場合はディレクトリと見なし、フラグをセットしてパスから除去
		if ($dirpos === (strlen($value) - 1)) {
			$value = substr($value, 0, $dirpos);
			$dirpos = strrpos($value, self::SEPARATOR);
			$options['isDir'] = '1';
		}

		if ($dirpos === false) {
			$options['dirname'] = '.';
			$options['basename'] = $value;
		} else {
			$options['dirname'] = substr($value, 0, $dirpos);
			$options['basename'] = substr($value, $dirpos + 1);
		}

		if (!isset($options['isDir'])) {
			$options['isDir'] = '0';
		}

		if (false !== ($extpos = strrpos($options['basename'], '.'))) {
			$options['extension'] = substr($options['basename'], $extpos + 1);
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
	 * URIの値を文字列で返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->value;
	}

	/**
	 * パスに含まれるマルチバイト文字をURLエンコードした値を返します。
	 *
	 * @return string
	 */
	public function urlencode()
	{
		return implode(
			self::SEPARATOR,
			array_map(
				function($value) {
					return rawurlencode($value);
				},
				explode(self::SEPARATOR, $this->value)
			)
		);
	}

	/**
	 * ファイルの親ディレクトリを返します。
	 *
	 * @return string
	 */
	public function getDirname()
	{
		return $this->dirname;
	}

	/**
	 * ファイルのベース名を返します。
	 *
	 * @return string
	 */
	public function getBasename()
	{
		return $this->basename;
	}

	/**
	 * ファイルの拡張子を返します。
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * 画像かどうかを返します。
	 *
	 * @return bool
	 */
	public function isImage()
	{
		return ($this->extension !== null)
			? in_array(strtolower($this->extension), array('jpg','jpeg','gif','png'))
			: false;
	}

	/**
	 * ディレクトリかどうかを返します。
	 *
	 * @return bool
	 */
	public function isDir()
	{
		return ($this->isDir !== null)
			? $this->isDir === '1'
			: false;
	}

	/**
	 * パスの階層をたどるイテレータを返します。
	 *
	 * @return \ArrayIterator of self
	 */
	public function getIterator()
	{
		$segments = explode(self::SEPARATOR, $this->value);
		$leafIndex = count($segments) - 1;
		$self = $this;
		return new \ArrayIterator(
			array_reduce(
				$segments,
				function($filepaths, $item) use ($leafIndex, $self) {
					$currentIndex = count($filepaths);
					if ($currentIndex !== $leafIndex) {
						// リーフノードではない場合はディレクトリフラグを付与する
						$filepaths[] = new self(
							$path = ($currentIndex === 0)
								? $item
								: $filepaths[$currentIndex - 1]->getValue() . self::SEPARATOR . $item,
								array('isDir' => '1')
						);
					} else {
						$filepaths[] = $self;
					}
					return $filepaths;
				},
				array()
			)
		);
	}

	private function regularizeValue($value)
	{
		if (DIRECTORY_SEPARATOR !== self::SEPARATOR) {
			$value = str_replace(DIRECTORY_SEPARATOR, self::SEPARATOR, $value);
		}
		if (strncmp($value, self::SEPARATOR, 1) === 0) {
			$value = substr($value, 1);
		}
		return $value;
	}

}
