<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright   Copyright (c) 2008-2016 Windwork Team. (http://www.windwork.org)
 * @license     http://opensource.org/licenses/MIT	MIT License
 */

/**
 * 获取应用实例
 * 
 * @return \wf\mvc\App
 */
function app() {
	return \wf\mvc\App::getInstance();
}

/**
 * 获取系统配置信息
 * @param string $name = null 配置项下标，为null则获取全部
 * @param string $namespace = ''
 * @return NULL|mixed
 */
function cfg($name = null, $namespace = '') {
	$cfgObj = \wf\mvc\App::getInstance()->getConfig();
	
	if ($name) {
		return $cfgObj->get($name, $namespace);
	}
	
	if ($namespace) {
		return $cfgObj->get($namespace);
	}

	return $cfgObj->getAll();
}

/**
 * 获取语言包中的字符串
 * @param string $key
 * @return string
 */
function lang($key) {
	return \wf\core\Lang::get($key);
}

/**
 * 获取用户请求参数
 * @param string  $key 获取的变量名
 * @param mixed   $default 默认值
 * @return mixed
 */
function req($key = null, $default = null) {
    return app()->getRequest()->getRequest($key, $default);
}

/**
 * 获取缓存实例
 * 
 * @return \wf\cache\ACache
 */
function cache() {
	return \wf\cache\CacheFactory::create(cfg());
}

/**
 * 写入日志
 *
 * 可以在config/config.php中启用日志，所有日志按类别保存
 * @param string $level 日志级别 emergency|alert|critical|error|warning|notice|info|debug
 * @param string $message 日志内容，如果是非标量则使用var_export成字符串保存
 */
function logging($level, $message) {
	$logger = \wf\logger\LoggerFactory::create(cfg());
	return $logger->log($level, $message);
}

/**
 * 获取数据库操作实例
 * 
 * @param string $id = 'default'
 * @return \wf\db\IDB
 */
function db($id = 'default') {
	return \wf\db\DBFactory::create(cfg(null, 'db'), $id);
}

/**
 * 获取存贮组件实例
 * 
 * @return \wf\storage\AStorage
 */
function storage() {
	return \wf\storage\StorageFactory::create(cfg());
}

/**
 * 获取缩略图的URL，一般在模板中使用
 * @param string|ing $path 图片路径或图片附件id
 * @param int $width = 100 为0时按高比例缩放
 * @param int $height = 0 为0时按宽比例缩放
 * @return string
 */
function thumb($path, $width = 100, $height = 0) {
	return storage()->getThumbUrl($path, $width, $height);
}

/**
 * 根据上传文件的Path获取完整URL
 * @param string $path
 * @return string
 */
function storageUrl($path) {
	return storage()->getFullUrl($path);
}

/**
 * 生成URL，一般在模板中使用
 *
 * @param string $uri
 * @param bool $fullUrl = false 是否获取完整URL
 * @return string
 */
function url($uri, $fullUrl = false) {
	return \wf\mvc\App::getInstance()->getRouter()->createUrl($uri, [], $fullUrl);
}

/**
 * 获取会员头像的url，一般在模板中使用
 *
 * @param int $uid
 * @param string $type big|medium|small|tiny
 * @param bool $reload 浏览时是否重新加载头像
 * @return string
 */
function avatar($uid, $type = 'small', $reload = false) {
	$urlExt = cfg('rewrite_ext');
	// => "storage/avatar/{$type}/{$uid}.jpg"
	$avatar = url("system.uploader.load/avatar/{$type}/{$uid}.jpg");

	$urlExt && $urlExt != '.jpg' && $avatar = preg_replace("/{$urlExt}$/", '', $avatar);

	if($reload) {
		static $rand = null;
		$rand or $rand = sprintf("%X", mt_rand(0x100000, 0xFFFFFF));

		$avatar .= "?".$rand;
	}

	return $avatar;
}

/**
 * 对请求URL进行解码
 * @param string $str
 * @return string||array
 */
function paramDecode($arg) {
	if (is_array($arg)) {
		foreach ($arg as $key => $val) {
			$arg[$key] = paramDecode($val);
		}
	} else {
		$arg = urldecode(urldecode($arg));
	}
	return $arg;
}

/**
 * 对请求URL进行编码
 * @param string $arg
 * @return string
 */
function paramEncode($arg) {
	if (is_array($arg)) {
		foreach ($arg as $key => $val) {
			$arg[$key] = paramEncode($val);
		}
	} else {
		$arg = urlencode(urlencode(paramDecode($arg)));
	}
	return $arg;
}


/**
 * 自动加载类
 * @param string $class
 */
function wfAutoload($class) {
	$class = '\\' . $class;

	// wf框架组件源码放到组件文件夹下的lib子文件夹
	if(preg_match("/^(\\\\wf\\\\[a-z0-9]+\\\\)(.+)/i", $class, $match)) {
		$lib = "{$match[1]}lib\\{$match[2]}";
		$lib = strtr($lib, '\\', DIRECTORY_SEPARATOR);
		$file = dirname(WF_PATH) . $lib . '.php';
			
		if (is_file($file)) {
			return include $file;
		}
	}

	// 通用加载文件方式，命名空间与文件夹对应
	$file = SRC_PATH . strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

	if (is_file($file)) {
		return include $file;
	}

	return false;
}
	
/**
 * 默认异常处理
 *
 * @param Exception $e 异常对象
 */
function exceptionHandler($e) {
	if (in_array($e->getCode(), array(401, 403, 404))) {
		$app = \wf\mvc\App::getInstance();
		$app->dispatch('system.misc.error/' . $e->getCode());			
		return ;
	}
	
	$message = $e->getMessage();
	$message = "<b style='color:#F00; font-size:14px; line-height:18px;'>{$message}</b>";
	
	$file = $e->getFile();
	$file = str_replace([dirname(dirname(WF_PATH)), WEB_ROOT], '', $file);
	$file = ltrim(str_replace('\\', '/', $file), '/');
	
	$line = $e->getLine();
	
	$trace = $e->getTraceAsString();
	$trace = str_replace([dirname(dirname(WF_PATH)), WEB_ROOT], '', $trace);
	$trace = "<pre class=\"error-trace\">{$trace}</pre>\n";

	if (ENV == 'dev') {
		$message = "<div style=\"color:#666;\">"
		        . "  <b>Exception:</b> ".get_class($e) . "\n<br />"
				. "  <b>Message:</b> {$message}\n<br />"
				. "  <b>File:</b> {$file}\n<br />"
				. "  <b>Line:</b> {$line}</b>"
				. "  {$trace}\n"
         		. "</div>";
	}
	
	header('Content-Type: text/html; Charset=utf-8');
	print "<div style=\"border: 1px solid #F90; color:#999; padding: 8px 12px; margin:20px 12px; background:#FFFEEE;\">{$message}</div>\n";

	logging('exception', $e->__toString()."\n");
}
