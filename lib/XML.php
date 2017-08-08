<?php
/**
 * Windwork
 * 
 * 一个用于快速开发高并发Web应用的轻量级PHP框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util;

/**
 * Xml操作，基于SimpleXML
 *
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class XML {
	/**
	 * 数组或对象生成 SimpleXMLElement对象实例
	 *
	 * @param mixed $object variable object to convert
	 * @param string $root root element name
	 * @param object $xml xml object
	 * @param string $unknown element name for numeric keys
	 * @param string $doctype XML doctype
	 * @return \SimpleXMLElement
	 */
	public static function make($object, $root = 'data', $xml = null, $unknown = 'element', $doctype = "<?xml version='1.0' encoding='utf-8'?>\n") {
		if(is_null($xml)) {
			$xml = new \SimpleXMLElement("$doctype<$root/>");
		}

		foreach((array) $object as $k => $v) {
			if(is_numeric($k)) {
				$k = $unknown;
			}

			if(is_scalar($v)) {
				$xml->addChild($k, htmlspecialchars($v, ENT_QUOTES, 'utf-8'));
			} else {
				$v = (array) $v;
				$node = is_numeric($k) ? $xml : $xml->addChild($k);
				self::make($v, $k, $node);
			}
		}

		return $xml;
	}

}
