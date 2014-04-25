<?php
/**
 * バリューオブジェクト
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Acme\Domain\Value;

use Acme\Domain\Value\AbstractValue;
use Acme\Domain\Value\ValueInterface;

/**
 * URI
 *
 * @author k.holy74@gmail.com
 */
class Uri extends AbstractValue implements ValueInterface, \ArrayAccess
{

	private static $pattern = '~\A(?:([^:/?#]+):)*(?://([^/?#]*))*([^?#]*)(?:\?([^#]*))?(?:#(.*))?\z~i';

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var string
	 */
	protected $scheme;

	/**
	 * @var string
	 */
	protected $host;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $query;

	/**
	 * @var string
	 */
	protected $fragment;

	/**
	 * __construct()
	 *
	 * @param mixed 値
	 * @param array オプション
	 */
	public function __construct($value = null, array $options = array())
	{
		if ($value === null) {
			$value = $this->build($options);
		}

		$uris = $this->parse($value);

		if (isset($uris['scheme']) && !isset($options['scheme'])) {
			$options['scheme'] = $uris['scheme'];
		}

		if (isset($uris['host']) && !isset($options['host'])) {
			$options['host'] = $uris['host'];
		}

		if (isset($uris['path']) && !isset($options['path'])) {
			$options['path'] = $uris['path'];
		}

		if (isset($uris['query']) && !isset($options['query'])) {
			$options['query'] = $uris['query'];
		}

		if (isset($uris['fragment']) && !isset($options['fragment'])) {
			$options['fragment'] = $uris['fragment'];
		}

		$this->initialize($value, $options);
	}

	/**
	 * URIの値を文字列で返します。
	 *
	 * @return string
	 */
	public function __toString()
	{
		return ($this->value !== null)
			? $this->value
			: $this->build(array(
				'scheme' => $this->scheme,
				'host' => $this->host,
				'path' => $this->path,
				'query' => $this->query,
				'fragment' => $this->fragment,
			));
	}

	/**
	 * パスの拡張子を返します。
	 *
	 * @param string 拡張子
	 */
	public function getExtension()
	{
		return ($this->path !== null)
			? pathinfo($this->path, PATHINFO_EXTENSION)
			: null;
	}

	private function parse($uri)
	{
		$uris = array();
		if (!preg_match(self::$pattern, $uri, $matches)) {
			throw new \InvalidArgumentException(
				sprintf('Invalid uri:%s', $uri)
			);
		}
		if (isset($matches[1])) {
			$uris['scheme'] = $matches[1];
		}
		if (isset($matches[2])) {
			$uris['host'] = $matches[2];
		}
		if (isset($matches[3])) {
			$uris['path'] = $matches[3];
		}
		if (isset($matches[4])) {
			$uris['query'] = $matches[4];
		}
		if (isset($matches[5])) {
			$uris['fragment'] = $matches[5];
		}
		return $uris;
	}

	private function build($uris)
	{
		$uri = '';
		if (isset($uris['scheme'])) {
			$uri .= $uris['scheme'] . '://';
		}
		if (isset($uris['host'])) {
			$uri .= $uris['host'];
		}
		if (isset($uris['path'])) {
			$uri .= $uris['path'];
		}
		if (isset($uris['query'])) {
			$uri .= '?' . $uris['query'];
		}
		if (isset($uris['fragment'])) {
			$uri .= '#' . $uris['fragment'];
		}
		return $uri;
	}

}
