<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */

/**
 * Windwork开发框架帮助函数，
 * 为了简化Windwork框架组件对象访问，提升开发效率和代码可读性。
 * 
 * @package     wf.web
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */

/**
 * 获取应用实例
 * 
 * @return \wf\web\Application
 */
function app() {
	return \wf\web\Application::app();
}

/**
 * 应用注入容器
 * @return \wf\core\DIContainer
 */
function di() {
    return app()->getDi();
}

/**
 * 调度器实例
 * 
 * @return \wf\web\Dispatcher
 */
function dsp() {
    return app()->getDispatcher();
}

/**
 * 读取配置变量，该函数第n个参数分别对应数组第n维下标，最多支持直接访问到第5维
 * <pre>
 * // 访问 $this->configs['url']
 * $conf->get('url');
 *
 * // 访问 $this->configs['url']['rewrite']
 * $conf->get('url', 'rewrite');
 *
 * // 访问 $this->configs['url']['rewrite']['login']
 * $conf->get('url', 'alias', 'login');
 * </pre>
 *
 * @param string $index0 = null
 * @param string $index1 = null
 * @param string $index2 = null
 * @param string $index3 = null
 * @param string $index4 = null
 * @return NULL|mixed
 */
function cfg($name = null) {
	$cfgObj = app()->getConfig();
	
	if ($name === null) {
	    return $cfgObj->getAll();
	}

	return $cfgObj->get($name);
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
 * 获取缓存实例
 * 
 * @return \wf\cache\ACache
 */
function cache() {
	return di()->cache();
}

/**
 * 写入日志
 *
 * 可以在config/config.php中启用日志，所有日志按类别保存
 * @param string $level 日志级别 emergency|alert|critical|error|warning|notice|info|debug
 * @param string $message 日志内容，如果是非标量则使用var_export成字符串保存
 */
function logging($level, $message) {
	return di()->logger()->log($level, $message);
}

/**
 * 获取数据库操作实例
 * 
 * @param string $id = 'default'
 * @return \wf\db\IDB
 */
function db($id = 'default') {
	return di()->db($id);
}

/**
 * 获取存贮组件实例
 * 
 * @return \wf\storage\AStorage
 */
function storage() {
	return di()->storage();
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
 * 根据上传文件的Path获取完整URL
 * @param string $path
 * @return string
 */
function storagePath($url) {
	return storage()->getPathFromUrl($url);
}

/**
 * 生成URL
 *
 * @param string $uri
 * @param bool $fullUrl = false 是否获取完整URL
 * @return string
 */
function url($uri, $fullUrl = false) {
	return dsp()->getRouter()->createUrl($uri, [], $fullUrl);
}

/**
 * 创建记录查询分页导航对象
 * @param int $totals 总记录数
 * @param int $rows = 10  每页显示记录数
 * @param string $tpl = 'simple' 分页 导航模板，mobile）手机分页, simple）简单分页, complex）复杂分页 
 */
function pager($totals, $rows = 10, $tpl = 'simple') {
    $pager = new \wf\pager\Pager($totals, $rows, '', ['argSeparator' => '/', 'valSeparator' => ':', 'tpl' => $tpl]);
    return $pager;
}
	
/**
 * 获取记录列表
 * @param array $cdt = []
 * @param int $rows = 10 每页记录数
 * @param string $countField = '*' 统计字段
 * @return array (
 *   'list'  => $list,
 *   'total' => $total,
 *   'pages' => $pager->lastPage, // 总页数
 *   'pager' => instance of \wf\pager\Pager(),
 * )
 */
function modelPager(\wf\model\Model $m, $cdt = [], $rows = 10, $countField = '*') {
	$total = $m->find($cdt)->count($countField);
	$pager = new \wf\pager\Pager($total, $rows);
	
	$list = $m->find($cdt)->all($pager->offset, $pager->rows);
	
	return [
		'list'  => $list,
		'total' => $total,
	    'pages' => $pager->lastPage, // 总页数
		'pager' => $pager,
	];
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
	$urlExt = cfg('url.rewriteExt');
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
 * 默认异常处理
 *
 * @param Exception $e 异常对象
 */
function exceptionHandler($e) {	
	$message = $e->getMessage();
	$message = "<b style='color:#F00; font-size:14px; line-height:18px;'>{$message}</b>";
	
	$file = $e->getFile();
	$file = str_replace(ROOT_DIR, '', $file);
	$file = ltrim(str_replace('\\', '/', $file), '/');
	
	$line = $e->getLine();
	
	$trace = $e->getTraceAsString();
	$trace = str_replace(ROOT_DIR, '', $trace);
	$trace = "<pre class=\"error-trace\">{$trace}</pre>\n";

	$message = "<div style=\"color:#666;\">"
	        . "  <b>Exception:</b> ".get_class($e) . "\n<br />"
			. "  <b>Message:</b> {$message}\n<br />"
			. "  <b>File:</b> {$file}\n<br />"
			. "  <b>Line:</b> {$line}</b>"
			. "  {$trace}\n"
     		. "</div>";
			
	//@ob_end_clean();
	header('Content-Type: text/html; Charset=utf-8');
	print "<div style=\"border: 1px solid #F90; color:#999; padding: 8px 12px; margin:20px 12px; background:#FFFEEE;\">{$message}</div>\n";
}

/**
 * 检查页面请求令牌
 * @return bool
 */
function checkToken() {
	$hash = dsp()->getRequest()->getRequest('hash');
	
	return \wf\util\Csrf::checkToken($hash);	
}
