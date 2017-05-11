<?php
/**
 * Windwork
 * 
 * 一个开源的PHP轻量级高效Web开发框架
 * 
 * @copyright Copyright (c) 2008-2017 Windwork Team. (http://www.windwork.org)
 * @license   http://opensource.org/licenses/MIT
 */
namespace wf\util;

/**
 * 模型帮助类 
 * 
 * @package     wf.web
 * @author      cm <cmpan@qq.com>
 * @link        http://docs.windwork.org/manual/wf.web.helper.html
 * @since       0.1.0
 */
class JSOutput {
	
	/**
	 * 输出js内容
	 *
	 * @param string $js js程序代码
	 * @return string
	 */
	public static function jsScript($js) {
		return "<script type='text/javascript'>{$js}</script>\n";
	}
	
	/**
	 * js跳转
	 *
	 * @param string $url
	 * @param bool $waitTime = 0 等待多时秒
	 * @return string
	 */
	public static function jsLocation($url, $waitTime = 0) {
		$url = urldecode(urldecode($url));
		$url = str_replace("'", "\\'", $url);
		
		if($waitTime) {
		    $waitTime = $waitTime * 1000;
		    return static::jsScript("setTimeout(function(){window.location.href='{$url}';}, {$waitTime});");
		} else {
		    return static::jsScript("window.location.href='{$url}'");
		}
	}
	
	/**
	 * 把内容转换成提供js的document.write()使用的字符串
	 * 
	 * @param string $content
	 */
	public static function jsWrite($content) {		
		$search  = array("\r\n", "\n", "\r", "\"", "<script ");
		$replace = array(' ', ' ', ' ', '\"', '<scr"+"ipt ');
        $content = str_replace($search, $replace, $content);
        
		return "document.write(\"{$content}\");\n";
	}

	/**
	 * 生成json
	 * 
	 * @param string $array
	 */
	public static function asJson($array, $jsonOption = null) {
		$json = json_encode($array, $jsonOption);

		$req = dsp()->getRequest();
		$iframeCallback = $req->getRequest('iframe_callback');

		header('Content-Type: text/html; Charset=utf-8');
		if ($iframeCallback) {
			$callback = preg_replace("/[^0-9a-z_\\.]/i", '', $iframeCallback);
			$callback = preg_replace("/^parent\\./", '', $callback);
			$json = "<script type=\"text/javascript\">try{parent.{$callback}({$json});}catch(e){}</script>";
		} else {
			$ajaxCallback = $req->getRequest('ajax_callback');
			if(!$ajaxCallback) {
			    $ajaxCallback = $req->getRequest('callback');
			}
			
			if($ajaxCallback && $ajaxCallback != '?') {
				$callback = preg_replace("/[^0-9a-z_\\.]/i", '', $ajaxCallback);
			    $json = "try{{$callback}({$json});}catch(e){}";
			}
		}
		
		return $json;
	}
}