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
 * CSRF防范类
 * 
 * CSRF（Cross-site request forgery）跨站请求伪造，
 * 也被称为“One Click Attack”或者Session Riding，
 * 通常缩写为CSRF或者XSRF，通过伪装来自受信任用户的请求来利用受信任的网站。
 *
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class Csrf {
	/**
	 * 生成防跨站请求伪造验证的令牌
	 * 依赖于session
	 * 
	 * @return string
	 */
	public static function token() {
		if(empty($_SESSION['csrf-token'])) {
			// 做增删改操作时验证字符是否匹配
			// 通过$_GET/$_POST变量传输
			$_SESSION['csrf-token'] = base_convert(mt_rand(0x100000, 0xFFFFFF), 10, 16);
		}
		
		return $_SESSION['csrf-token'];
	}
	
	/**
	 * 验证防跨站请求伪造验证的令牌
	 * 
	 * @param string $token
	 * @return boolean
	 */
	public static function checkToken($token) {
		if (!$token || $token != static::token()) {
			return false;
		}
		
		return true;
	}
}