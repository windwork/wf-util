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
 * 验证类
 * 
 * @package     wf.util
 * @author      erzh <cmpan@qq.com>
 * @since       0.1.0
 */
class Validator {
	/**
	 * 批量验证是否有错误
	 * @param array $data 
	 * @param array $rules 验证规则  array('待验证数组下标' => array('验证方法1' => '提示信息1', '验证方法2' => '提示信息2'), ...)
	 * @param bool $validAll = true 是否验证所有属性，如果为false只验证到第一个不符合规则的属性就停止验证
	 * @return array 错误信息，返回空数组则验证通过
	 */
	public static function validErr($data, $rules, $validAll = true) {
		$aliases = [
			'empty' => 'isEmpty',
		];
		
		$validErrs = array();
		foreach ($rules as $key => $fieldRule) {
			// 为空并且允许为空则不检查
			if(empty($data[$key]) && !array_key_exists('notEmpty', $fieldRule)) {
				continue;
			}
			
			// 待验证字符串
			$string = $data[$key];
			
			foreach ($fieldRule as $method => $msg) {
				$method = trim($method);
				
				// 支持别名
				if (isset($aliases[$method])) {
					$method = $aliases[$method];
				}
				
				// 自定义正则，下标第一个字符不是字母
				// 自定义格式必须是以正则匹配规则作为下标，提示消息作为值
				if (preg_match("/[^a-z]/i", $method[0])) {
					if(!preg_match($method, $data[$key])) {
						$validErrs[] = $msg;
					}
					
					continue;
				}
				
				$callback = "static::{$method}";
				
				if (is_array($msg)) {
				    $isNot  = !empty($msg['not']);
				    $valid  = call_user_func_array($callback, [$string, $msg]);
				    
					if(($isNot && $valid) || (!$isNot && !$valid)) {
						$validErrs[] = $msg['msg'];
						
						if (!$validAll) {
							return $validErrs;
						}
					}
				} elseif (!call_user_func($callback, $string)) {
					// 验证方法只有待验证参数一个一个参数
					$validErrs[] = $msg;
					
					if (!$validAll) {
						return $validErrs;
					}
				}
			}
		}
		
		return $validErrs;
	}
	
	/**
	 * 参数格式是否email格式
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function email($email) {
		return strpos($email, "@") !== false && strpos($email, ".") !== false &&
		    (bool)preg_match("/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i", $email);
	}

	/**
	 * 参数格式是否是时间的格式 Y-m-d H:i:s
	 *
	 * @param string $time
	 * @return bool
	 */
	public static function time($time) {
		return (bool)preg_match("/[\\d]{4}-[\\d]{1,2}-[\\d]{1,2}\\s+[\\d]{1,2}:[\\d]{1,2}:[\\d]{1,2}/", $time);
	}

	/**
	 * 参数是否为空，不为空则验证通过
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function notEmpty($var) {
		return !empty($var);
	}

	/**
	 * 参数为空则验证通过
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function isEmpty($var) {
		return empty($var);
	}

	/**
	 * 参数是否是只允许字母、数字和下划线的字符串
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function safeString($var) {
		return (bool)preg_match('/^[0-9a-zA-Z_]*$/', $var);
	}

	/**
	 * 参数类型是否是货币的格式 123.45,保留2位小数
	 *
	 * @param string|float $var
	 * @return bool
	 */
	public static function money($var) {
		return (bool)preg_match('/^[0-9]*\.[0-9]{2}$/', $var);
	}

	/**
	 * 参数类型是否为IP
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function ip($var) {
		return (bool)ip2long((string)$var);
	}

	/**
	 * 是否是链接
	 * @param string $str
	 * @return number
	 */
	public static function url($str) {
		return (bool)preg_match("/^(http|https|ftp):\\/\\/(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}(\\/\\w)?/i", $str);		
	}

	/**
	 * 参数类型是否为数字型
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function number($var) {
		return is_numeric($var);
	}

	/**
	 * 参数类型是否为年的格式(1000-2999)
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function year($var) {
		return (bool)preg_match('/^[12][0-9]{3}$/', $var) && strlen($var) == 4;
	}

	/**
	 * 参数类型是否为月格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function month($var) {
		return is_numeric($var) && $var > 0 && $var <= 12 && strlen($var) <= 2;
	}

	/**
	 * 参数类型是否为日期的日格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function day($var) {
		return is_numeric($var) && $var > 0 && $var <= 31 && strlen($var) <= 2;
	}

	/**
	 * 参数类型是否为时间的小时格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function hour($var) {
		return is_numeric($var) && $var >= 0 && $var <= 23 && strlen($var) <= 2;
	}

	/**
	 * 参数类型是否为时间的分钟格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function minute($var) {
		return is_numeric($var) && $var >= 0 && $var < 60 && strlen($var) <= 2;
	}

	/**
	 * 参数类型是否为时间的秒钟格式
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function second($var) {
		return self::minute($var);
	}

	/**
	 * 参数类型是否为星期范围内的值
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function week($var) {
		$weeks = array(1, 2, 3, 4, 5, 6, 7, '１', '２', '３', '４', '５', '６', '７', '一', '二', '三', '四', '五', '六', '天', '日', 'monday', 
				'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'mon', 'tue', 
				'wed', 'thu', 'fri', 'sat', 'sun');
		$var = strtolower($var);
		
		return in_array($var, $weeks);
	}

	/**
	 * 参数类型是否为十六进制字符串
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function hex($var) {
		return (bool)preg_match('/^[0-9A-Fa-f]*$/', ltrim($var, '-'));
	}

	/**
	 * 身份证号码
	 * 可以验证15和18位的身份证号码
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function idCard($var) {
		$province = array("11", "12", "13", "14", "15", "21", "22", "23", "31", "32", "33", "34", 
				"35", "36", "37", "41", "42", "43", "44", "45", "46", "50", "51", "52", "53", 
				"54", "61", "62", "63", "64", "65", "71", "81", "82", "91");
		//前两位的省级代码
		if(! in_array(substr($var, 0, 2), $province)) {
			return false;
		}
		
		if(strlen($var) == 15) {
			if(!preg_match("/^\\d+$/", $var)) {
				return false;
			}
			// 检查年-月-日（年前面加19）
			return checkdate(substr($var, 8, 2), substr($var, 10, 2), "19" . substr($var, 6, 2));
		}
		if(strlen($var) == 18) {			
			if(!preg_match("/^\\d+$/", substr($var, 0, 17))) {
				return false; // 前17位是否是数字
			}
			//检查年-月-日
			if(! @checkdate(substr($var, 10, 2), substr($var, 12, 2), 
					substr($var, 6, 4))) {
				return false;
			}
			//加权因子Wi=2^（i-1）(mod 11)计算得出
			$Wi_arr = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
			//校验码对应值
			$VN_arr = array(1, 0, 'x', 9, 8, 7, 6, 5, 4, 3, 2);
			//计算校验码总值(计算前17位的，最后一位为校验码)
			$t = '';

			$var = strtolower($var);
			for($i = 0; $i < strlen($var) - 1; $i++) {
				$t += substr($var, $i, 1) * $Wi_arr[$i];
			}
			//得到校验码
			$VN = $VN_arr[($t % 11)];
			//判断最后一位的校验码
			if($VN == substr($var, - 1)) {
				return true;
			} else {
				return false;
			}
		}
		
		return false;
	}

	/**
	 * 验证字符串是否是utf-8
	 *
	 * @param string $text
	 * @return bool
	 */
	public static function utf8($text) {
		if(strlen($text) == 0) {
			return true;
		}
		
		return (preg_match('/^./us', $text) == 1);
	}
	
	/**
	 * 检查日期格式是否正确
	 * 
	 * @param string $text 日期，如：2011-01-20
	 * @param string $delemiter 日期分隔符
	 */
	public static function date($text, $delemiter = '-') {		
    	return (bool)preg_match("/^[\\d]{4}\\{$delemiter}[\\d]{1,2}\\{$delemiter}[\\d]{1,2}$/", $text);
	}
	
	/**
	 * 是否是手机号
	 * 
	 * @param number $mobile
	 * @return bool
	 */
	public static function mobile($mobile) {
		return (bool)preg_match("/^1[34578]{1}[0-9]{9}$/", $mobile) || (bool)preg_match("/^17[6-8]{1}[0-9]{8}$/", $mobile);
	}
	
	/**
	 * 字符串长度不超过
	 * @param string $text
	 * @param array $arg
	 */
	public static function maxLen($text, array $args) {
		return strlen($text) <= $args['maxLen'];
	}
	
	/**
	 * 字符串长度不小于
	 * @param string $text
	 * @param int $min
	 */
	public static function minLen($text, array $args) {
		return strlen($text) >= $args['minLen'];
	}
	
	/**
	 * 值不大于
	 * @param number $val
	 * @param int $max
	 */
	public static function max($val, array $args) {
		return $val <= $args['max'];
	}
	
	/**
	 * 值不小于
	 * @param number $val
	 * @param int $min
	 */
	public static function min($val, array $args) {
		return $val >= $args['min'];
	}
		
}
