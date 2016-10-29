<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2015 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */
namespace wf\util;

/**
 * 服务器端相关信息
 * @author cm
 *
 */
class Env {

	/**
	 * 获取服务器真实ip
	 *
	 * @return string
	 */
	public static function getServerIP() {
		static $serverIP = null;
	
		if ($serverIP !== null) {
			return $serverIP;
		}
	
		if (isset($_SERVER)) {
			if (isset($_SERVER['SERVER_ADDR'])) {
				$serverIP = $_SERVER['SERVER_ADDR'];
			} else {
				$serverIP = '0.0.0.0';
			}
		} else {
			$serverIP = getenv('SERVER_ADDR');
		}
	
		return $serverIP;
	}
		
	/**
	 * 当前php进程占用的内存（M）, 四舍五入到小数点后4位
	 * 
	 * @return float
	 */
	public static function getMemUsed() {
		if (function_exists('memory_get_usage')) {
			return round(memory_get_usage()/(1024*1024), 4); // by M
		} else {
			return 0;
		}
	}
	
	/**
	 * 是否启用gz压缩，服务器端支持压缩并且客户端支持解压缩则启用压缩
	 * @return bool
	 */
	public static function isGzEnabled() {
		static $isGzEnabled = null;
		if (null === $isGzEnabled) {
			// 配置文件中启用gzip
			$isGzEnabled = cfg('gzcompress') 
			// 客户端支持gzip
			  && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false
			  // 服务器端支持gzip
			  && (ini_get("zlib.output_compression") == 1 || in_array('ob_gzhandler', ob_list_handlers()));
		}
		
		return $isGzEnabled;
	}
}
