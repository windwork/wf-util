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

class Str {

	/**
	 * 把字符串左边给定的字符串去掉
	 *
	 * @param string $string
	 * @param string $needle
	 * @param bool $trimAll 是否把重复匹配的都去掉
	 */
	public static function ltrim($string, $needle, $trimAll = false) {
		// 如果字符串左边字符串和左边要去掉的字符串一致则去掉
		if($needle === substr($string, 0, strlen($needle))){
			$string = substr($string, strlen($needle));
			$trimAll && $string = self::ltrim($string, $needle, true);
		}
	
		return $string;
	}
	
	/**
	 * 把字符串右边给定的字符串去掉
	 *
	 * @param string $string
	 * @param string $needle
	 * @param bool $trimAll 是否把重复匹配的都去掉
	 * @return string
	 */
	public static function rtrim($string, $needle, $trimAll = false) {
		// 如果字符串右边字符串和要去掉的字符串一致则去掉
		if($needle === substr($string,  - strlen($needle))){
			$string = substr($string, 0,  - strlen($needle));
			$trimAll && $string = self::rtrim($string, $needle, true);
		}
	
		return $string;
	}
	
	/**
	 * 把字符串两头给定的字符串去掉
	 *
	 * @param string $string
	 * @param string $needle
	 * @param bool $trimAll 是否把重复匹配的都去掉
	 * @return string
	 */
	public static function trim($string, $needle, $trimAll = true) {
		$string = self::rtrim($string, $needle, $trimAll);
		$string = self::ltrim($string, $needle, $trimAll);
	
		return $string;
	}
	
	/**
	 * 把全角字符全部换成半角字符再返回
	 *
	 * @param string $str
	 * @return string
	 */
	public static function toSemiangle($str) {
		$arr = array(
				"０" => "0", "１" => "1", "２" => "2", "３" => "3", "４" => "4", "５" => "5",
				"６" => "6", "７" => "7", "８" => "8", "９" => "9", "Ａ" => "A", "Ｂ" => "B",
				"Ｃ" => "C", "Ｄ" => "D", "Ｅ" => "E", "Ｆ" => "F", "Ｇ" => "G", "Ｈ" => "H",
				"Ｉ" => "I", "Ｊ" => "J", "Ｋ" => "K", "Ｌ" => "L", "Ｍ" => "M", "Ｎ" => "N",
				"Ｏ" => "O", "Ｐ" => "P", "Ｑ" => "Q", "Ｒ" => "R", "Ｓ" => "S", "Ｔ" => "T",
				"Ｕ" => "U", "Ｖ" => "V", "Ｗ" => "W", "Ｘ" => "X", "Ｙ" => "Y", "Ｚ" => "Z",
				"ａ" => "a", "ｂ" => "b", "ｃ" => "c", "ｄ" => "d", "ｅ" => "e", "ｆ" => "f",
				"ｇ" => "g", "ｈ" => "h", "ｉ" => "i", "ｊ" => "j", "ｋ" => "k", "ｌ" => "l",
				"ｍ" => "m", "ｎ" => "n", "ｏ" => "o", "ｐ" => "p", "ｑ" => "q", "ｒ" => "r",
				"ｓ" => "s", "ｔ" => "t", "ｕ" => "u", "ｖ" => "v", "ｗ" => "w", "ｘ" => "x",
				"ｙ" => "y", "ｚ" => "z", "（" => "(", "）" => ")", "［" => "[", "］" => "]",
				"【" => "[", "】" => "]", "〖" => "[", "〗" => "]", "「" => "[", "」" => "]",
				"『" => "[", "』" => "]", "｛" => "{", "｝" => "}", "《" => "<", "》" => ">",
				"％" => "%", "＋" => "+", "—" => "-", "－" => "-", "～" => "-", "：" => ":",
				"。" => ".", "、" => ",", "，" => ".", "、" => ".", "；" => ",", "？" => "?",
				"！" => "!", "…" => "-", "‖" => "|", "　" => " ", "＇" => "`", "｀" => "`",
				"｜" => "|", "〃" => "\"", "＂" => "\"");
		return strtr($str, $arr);
	}


	/**
	 * 将任意维度的数组值进行 htmlspecialchars
	 *
	 * @param array|string $data
	 * @param array $ignoreKeys 忽略的下标
	 * @param string $flag
	 * @return array|string
	 */
	public static function htmlspecialcharsDeep($data, $ignoreKeys = array(), $flag = ENT_NOQUOTES){
		if(is_array($data)) {
			foreach ($data as $key => $val) {
				if (is_array($val) && !in_array($key, $ignoreKeys)) {
					$data[$key] = htmlspecialcharsDeep($val, $ignoreKeys);
				} else if (!is_array($val)) {
					$data[$key]	= htmlspecialchars($val, $flag);
				}
			}
		} else {
			$data = htmlspecialchars_decode($data, $flag);
			$data = htmlspecialchars($data, $flag);
		}
	
		return $data;
	}

	/**
	 * 替换html标签
	 *
	 * @param string $string
	 * @param string|array $tag
	 * @param string|array $replace
	 * @param string $feed
	 * @return string
	 */
	public static function htmlTagReplace($string, $tag = 'p', $replace = 'div', $feed = '') {
		$tag = (array)$tag;
		$replace = (array)$replace;
		foreach ($tag as $key => $_tag) {
			$string = preg_replace("/<{$_tag}(.*?)>(.*?)<\/{$_tag}>/s", "<{$replace[$key]}\\1>\\2</{$replace[$key]}>", $string);
			$string = preg_replace("/<{$_tag}(.*?)\/>/s", "<{$replace[$key]}\\1/>", $string);
		}
	
		return $string ;
	}
	
	/**
	 * 取得随机整数
	 *
	 * @param int $len
	 * @return int
	 */
	public static function randNum($len = 4) {
		$rand = mt_rand(1, 9);
	
		for($i = 0; $i < $len - 1; $i++) {
			$rand .= mt_rand(0, 9);
		}
	
		return $rand;
	}
	
	/**
	 * 取得无序随机字符串
	 *
	 * @param int $len
	 * @param bool $easyRead = false 是否是人眼容易识别的
	 * @return int
	 */
	public static function randStr($len, $easyRead = false) {		
		if ($easyRead) {
		    $str = 'ab23456789abcdefghzykxmnwpqrstuvwxyz'; // 人眼容易识别的字母数字
		} else {
			$str = '0123456789abcdefghijklmnopqrstuvwxyz';
		}
	
		$r = '';
		for ($i = 0; $i < $len; $i++) {
			$r .= $str[mt_rand(0, 35)];
		}
	
		return $r;
	}
	
	/**
	 * 生成UUID
	 *
	 * @param int $type = 36 uuid类型 16|32|36  36:标准的UUID；32：没有横杠的32个16进制字符；16：16个16进制字符；
	 */
	public static function guid($type = 36) {
		$pieces = array(
				crc32(uniqid()),
				mt_rand(0x1000, 0xffff),
				mt_rand(0x1000, 0xffff),
				mt_rand(0x1000, 0xffff),
				mt_rand(0x1000, 0xffff),
				mt_rand(0x1000, 0xffff),
		);
	
		// 返回小写的16进制字符串（大写对密集型恐惧症人有更大的压迫感^_^）
		switch ($type) {
			case 16:
				$format = "%08x%x%x";
				break;
			case 32:
				$format = "%08x%x%x%x%x%x";
				break;
			default:
				$format = "%08x-%x-%x-%x-%x%x";
		}
	
		$uuid = vsprintf($format, $pieces);
		return $uuid;
	}


	/**
	 * 给内容中的关键词添加锚点
	 * @param string $content
	 * @param string $url
	 * @param string $keywords
	 * @return mixed
	 */
	public static function addAnchor($content, $url, $keywords) {
		$keywords = static::keywordsFormat($keywords);
		
		if (!$keywords) {
			return $content;
		}
	
		$keywords = static::keywordsFormat($keywords);
		$anchors = explode(',', $keywords);
	
		// 把内容中的所有连接先进行base64编码
		$content = preg_replace_callback("/(<a.+?>.*?<\\/a>)/i", function($matchs) {
			return "'<[base['.base64_encode('{$matchs[1]}').']]>'";
		}, $content);
	
		foreach ($anchors as $anchor) {
			if (!$anchor) {
				continue;
			}
				
			if(false === $pos = strpos($content, $anchor)) {
				continue;
			}
	
			$name = urlencode($anchor);
			$replace = "<a href=\"{$url}#{$name}\" name=\"{$name}\" title=\"{$anchor}\">{$anchor}</a>";
				
			$content = substr($content, 0, $pos) . $replace . substr($content, $pos + strlen($anchor));
		}
	
		// 解码原本的链接内容
		$content = preg_replace_callback("/<\\[base\\[(.*?)\\]\\]>/i", function($matchs) {
			return "str_replace('\\\"', '\"', base64_decode('{$matchs[1]}'))";			
		}, $content);
	
		return $content;
	}
	
	/**
	 * 对字符串进行处理，供URL中作为参数使用
	 * @param string $slug
	 * @param string $default = '' 如果$slug为空时使用的值
	 * @param int $leng = 255  截取长度，应跟最终储存到数据库的字段长度一致
	 * @return string
	 */
	public static function stripSlug($slug, $default = '', $leng = 255) {
		if(!$slug && $default){
			$slug = $default;
		}
		
		if (!$slug) {
			return $slug;
		}
		
		$slug = strip_tags($slug);
		$slug = trim($slug);
		$slug = preg_replace("/(\\s+)/", "-", $slug);
		$slug = str_replace(array("&nbsp;", "\"","'", "\r", "\n", "\032", "/"), "", $slug);
		$slug = urlencode(urldecode(urldecode($slug)));

		$slug = substr($slug, 0, $leng);
		
		// url编码字符部分被截掉的处理
		if (@$slug[$leng - 1] == '%' || @$slug[$leng - 2] == '%') {
			$slug = substr($slug, 0, strrpos($slug, '%'));
		}
		
		$slug = strtolower($slug);
				
		return $slug;
	}
	
	/**
	 * 格式化简介文字
	 * @param string $str
	 * @param string $default = '' 默认内容
	 * @param number $leng = 50
	 * @return string
	 */
	public static function stripDesc($str, $default = '', $leng = 50) {
		$str || $str = $default;
	
		$str = trim(str_replace(array("&nbsp;", "\r", "\n"), "", strip_tags($str)));
		$str && $str = mb_substr($str, 0, $leng, "utf-8");
	
		return $str;
	}
	
	/**
	 * 去掉html标签
	 * @param string $string
	 * @param string $allowTags = '' 保留的标签
	 * @return string
	 */
	public static function stripTags($string, $allowTags = '') {
		$string = strip_tags($string, $allowTags);
		$string = str_replace('&nbsp;', '', $string);
		$string = trim($string);
		
		return $string;
	}
	
	/**
	 * 多个关键词（标签字符串）统一格式
	 * 
	 * 关键词直接用半角逗号隔开
	 * 
	 * @param string $keywords
	 * @return string
	 */
	public static function keywordsFormat($keywords) {
		$keywords = trim($keywords, ', ');
	
		if($keywords) {
			$keywords = htmlspecialchars(strip_tags($keywords)); // 去掉html和特殊符号
			$keywords = preg_replace("/(\\s|\\-|\\||，|、|　)/", ",", $keywords); // 将空格，全角符号转为半角符号
			$keywords = preg_replace("/,+/", ",", $keywords);
				
			// 去掉重复的标签
			$keywords = explode(',', $keywords);
			$keywords = array_unique($keywords);
			$keywords = implode(',', $keywords);
		}
	
		return $keywords;
	}
	
	/**
	 * 给html内容中的图片标签改为添加lazyload方式
	 * 
	 * @param string $string
	 * @param string $loadingGifUrl = 'static/images/lazy-loading.gif'
	 * @return string
	 */
	public static function addLazyLoadToContentImage($string, $cssClass = 'lazy', $loadingGifUrl = 'static/images/lazy-loading.gif') {
		if(preg_match_all("/<img\\s.*?>/is", $string, $matchAll)) {
			$matchArr = $matchAll[0];
			$matchArr = array_unique($matchArr);
			
			$replace = preg_replace("/(class=.*?)[\\s>\\/]/is", '', $matchArr);
			$replace = preg_replace("/\\ssrc=(.*?)/is", " class=\"{$cssClass}\" src=\"{$loadingGifUrl}\" data-original=$1", $replace);
			
			$string  = str_replace($matchArr, $replace, $string);
		}
		
		return $string;
	}


	/**
	 * 去掉数组或字符串的html标签
	 * @param string $arr
	 * @param string $allowTags
	 * @return string
	 */
	public static function stripTagsDeep($arr, $allowTags = '') {
		if (is_array($arr)) {
			foreach ($arr as $key => $val) {
				$arr[$key] = stripTagsDeep($val, $allowTags);
			}
		} else {
			$arr = static::stripTags($arr, $allowTags);
		}
	
		return $arr;
	}

	/**
	 * 生成t.cn短网址
	 * @param string $url
	 * @return mixed
	 */
	public static function shortUrl($url, $key = '2270845191') {
	    $r = file_get_contents("http://api.t.sina.com.cn/short_url/shorten.json?source={$key}&url_long={$url}");
	    
	    if($r) {
	        $items = json_decode($r);
	        return $items[0]->url_short;
	    }
	    
	    return '';
	}

	/**
	 * html属性过滤
	 * @param string $attr
	 * @param int $len = 0 大于0时截取指定长度的字符串返回
	 * @return string
	 */
	public static function htmlAttr($attr, $len = 0) {
		$attr = strip_tags($attr);
		$attr = htmlspecialchars($attr);
		$len && $attr = mb_substr($attr, 0, $len, 'utf-8');
		return $attr;
	}

	/**
	 * 密码加密
	 * @param string $str 密码明文
	 * @param string $salt 每个用户用于验证密码的不同的字符串，推荐生成： sprintf('%x', mt_rand(0x100000, 0xFFFFFF));
	 * @return string
	 */
	public static function pw($str, $salt) {
		return hash_hmac('sha1', $str, $salt);
	}
	
	/**
	 * 安全字符串，
	 * 去掉字母、数字、下划线以外的任何字符
	 * @param string $str
	 * @return mixed
	 */
	public static function safeString($str) {
		return preg_replace("/[^a-z0-9_]/is", '', $str);
	}
	
	/**
	 * 用于判断两个字符串是否相等（忽略大小写）
	 *
	 * @param string $str
	 * @param string $expect
	 * @return boolean
	 */
	public static function isEqual($str, $expect) {
		return strtolower((string)$str) === strtolower((string)$expect);
	}
	
	/**
	 * 转换编码
	 * @param string $str
	 * @param string $from
	 * @param string $to
	 */
	public static function convertEncoding($str, $from, $to) 
	{
	    if(function_exists('iconv')) {
	        return iconv($from, $to . '//ignore', $str);
	    } elseif(function_exists('mb_convert_encoding')) {
	        return mb_convert_encoding($str, $to, $from);
	    }
	    
	    throw new \Exception('请先安装PHP iconv或mb_string扩展');
	}
	
	/**
	 * 获取字符串长度
	 * @param string $str
	 * @param string $encoding = 'UTF-8'
	 * @throws \Exception
	 */
	public static function strlen($str, $encoding = 'UTF-8') {
	    if(function_exists('iconv_strlen')) {
	        return iconv_strlen($str, $encoding);
	    } elseif(function_exists('mb_strlen')) {
	        return mb_strlen($str, $encoding);
	    }
	    throw new \Exception('请先安装PHP iconv或mb_string扩展');
	}
	
	/**
	 * 截取字符串
	 * @param string $str
	 * @param int $len
	 * @param int $start
	 * @param string $encoding = 'UTF-8'
	 * @throws \Exception
	 */
	public static function substr($str, $len, $start = 0, $encoding = 'UTF-8') {
	    if(function_exists('iconv_substr')) {
	        return iconv_substr($str, $start, $len, $encoding);
	    } elseif(function_exists('mb_substr')) {
	        return mb_substr($str, $start, $len, $encoding);
	    }
	    throw new \Exception('请先安装PHP iconv或mb_string扩展');
	}
	
	/**
	 * 查找字符串第一次出现的位置
	 * @param string $haystack
	 * @param string $needle
	 * @param int $start = 0 开始查找的位置
	 * @param string $encoding = 'UTF-8'
	 * @throws \Exception
	 * @return string
	 */
	public static function strpos($haystack, $needle, $start = 0, $encoding = 'UTF-8') {
	    if(function_exists('iconv_substr')) {
	        return iconv_strpos($haystack, $needle, $start, $encoding);
	    } elseif(function_exists('mb_substr')) {
	        return mb_strpos($haystack, $needle, $start, $encoding);
	    }
	    throw new \Exception('请先安装PHP iconv或mb_string扩展');
	}
	
	/**
	 * 查找字符串最后一次出现的位置
	 * @param string $haystack
	 * @param string $needle
	 * @param int $start = 0 开始查找的位置
	 * @param string $encoding = 'UTF-8'
	 * @throws \Exception
	 * @return string
	 */
	public static function strrpos($haystack, $needle, $start = 0, $encoding = 'UTF-8') {
	    if(function_exists('iconv_substr')) {
	        return iconv_strrpos($haystack, $needle, $start, $encoding);
	    } elseif(function_exists('mb_substr')) {
	        return mb_strrpos($haystack, $needle, $start, $encoding);
	    }
	    throw new \Exception('请先安装PHP iconv或mb_string扩展');
	}
}