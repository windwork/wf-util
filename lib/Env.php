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
 * 服务器端相关信息
 * @author cm
 *
 */
class Env 
{

	/**
	 * 获取服务器真实ip
	 *
	 * @return string
	 */
	public static function getServerIP()
	{
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
	public static function getMemUsed()
	{
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
	public static function isGzEnabled()
	{
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
	
	/**
	 * 文件上传最大尺寸（M）
	 */
	public static function getUploadMaxSize()
	{
		static $uploadMaxSize;
		if ($uploadMaxSize) {
			return $uploadMaxSize;
		}
		
		// =>（M）
		$sizeToM = function($size) {
			$size = trim($size);
			
			if(preg_match("/([\\.\\d]+)M/i", $size, $match)) {
				// M => M
				return $match[1];
			} elseif (preg_match("/([\\.\\d]+)G/i", $size, $match)) {
				// G => M
				return $match[1] * 1024;
			} elseif (preg_match("/([\\.\\d]+)K/i", $size, $match)) {
				// K => M
				return number_format($match[1]/1024, 2);
			} else {
				// B => M
				return number_format($size/(1024*1024), 2);
			}
		};
		
		$uploadMaxFilesize = ini_get('upload_max_filesize');
		$postMaxSize       = ini_get('post_max_size');
		$cfgMaxSize        = cfg('srv.storage.sizeLimit');
		
		// 最小允许上传1M
		$cfgMaxSize || $cfgMaxSize = '1M';
		
		// 单位统一转成M后，取最小
		$uploadMaxSize = min($sizeToM($uploadMaxFilesize), $sizeToM($postMaxSize), $sizeToM($cfgMaxSize));
		
		return $uploadMaxSize . 'M';
	}
}
