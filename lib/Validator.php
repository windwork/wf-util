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
 * 验证类
 * 
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class Validator {
    /**
     * 
     * @var array
     */
    private $errors = [];
    
    /**
     * 获取批量匹配所有错误信息
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * 获取批量匹配规则最后一次错误信息
     * @return mixed
     */
    public function getLastError()
    {
        $errors = $this->errors;        
        return array_pop($errors);
    }
    
	/**
	 * 批量验证是否有错误
	 * @param array $data 
	 * @param array $rules 验证规则  ['待验证数组下标' => ['验证方法1' => '提示信息1', '验证方法2' => '提示信息2'], ...]
	 * @param bool $firstErrBreak = false 第一次验证不通过时返回
	 * @return bool
	 */
	public function validate(array $data, array $rules, $firstErrBreak = false)
	{
		$this->errors = [];
		foreach ($rules as $key => $fieldRule) {
			// 为空并且允许为空则不检查
			if(empty($data[$key]) && !array_key_exists('required', $fieldRule)) {
				continue;
			}
			
			// 待验证字符串
			$string = $data[$key];
			
			foreach ($fieldRule as $method => $msg) {
				$method = trim($method);
								
				// 自定义正则，下标第一个字符不是字母
				// 自定义格式必须是以正则匹配规则作为下标，提示消息作为值
				if (preg_match("/[^a-z]/i", $method[0])) {
					if(!preg_match($method, $data[$key])) {
						$this->errors[] = $msg;
					}
					
					continue;
				}
				
				$callback = "static::{$method}";
				
				if (is_array($msg)) {
				    $isNot  = !empty($msg['not']);
				    $valid  = call_user_func_array($callback, [$string, $msg]);
				    
					if(($isNot && $valid) || (!$isNot && !$valid)) {
						$this->errors[] = $msg['msg'];
						if ($firstErrBreak) {
						    return false;
						}
					}
				} elseif (!call_user_func($callback, $string)) {
					// 验证方法只有待验证参数一个一个参数
					$this->errors[] = $msg;
					if ($firstErrBreak) {
					    return false;
					}
				}
			}
		}
		
		return empty($this->errors);
	}
	
	/**
	 * 参数格式是否email格式
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function email($email)
	{
		return strpos($email, "@") !== false && strpos($email, ".") !== false &&
		    (bool)preg_match("/^[a-z0-9_\\-\\.]+@[a-z0-9_\\-\\.]+\\.[a-z]{2,8}\$/i", $email);
	}

	/**
	 * （notEmpty）参数是否为空，不为空则验证通过
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function required($var)
	{
		return !empty($var);
	}

	/**
	 * 参数是否是只允许字母、数字和下划线的字符串
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function safeString($var)
	{
		return (bool)preg_match('/^[0-9a-zA-Z_]*$/', $var);
	}

	/**
	 * 参数类型是否是货币的格式 123.45,保留2位小数
	 *
	 * @param string|float $var
	 * @return bool
	 */
	public static function money($var)
	{
		return (bool)preg_match('/^[0-9]+\\.[0-9]{2}$/', $var);
	}

	/**
	 * 参数类型是否为IP
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function ip($var)
	{
		return (bool)ip2long((string)$var);
	}

	/**
	 * 是否是链接
	 * @param string $str
	 * @return number
	 */
	public static function url($str)
	{
	    preg_match("/^(http|ftp)[s]?:\\/\\/[a-z0-9_\\-\\.]+\\.+[a-z]{2,5}(\\:[\\d]+)?\\/?[^\\s]*$/i", $str, $m);
		return (bool)preg_match("/^(http|ftp)[s]?:\\/\\/[a-z0-9_\\-\\.]+\\.+[a-z]{2,5}(\\:[\\d]+)?\\/?[^\\s]*$/i", $str);		
	}

	/**
	 * 参数类型是否为数字型
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function number($var)
	{
		return is_numeric($var);
	}

	/**
	 * 参数类型是否为年的格式(1-32767)
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function year($year)
	{
	    if (!is_numeric($year)) {
	        return false;
	    }
	    
		return checkdate(1, 1, $year);
	}

	/**
	 * 参数类型是否为月格式（1-12）
	 *
	 * @param int|string $month
	 * @return bool
	 */
	public static function month($month)
	{	    
	    return is_numeric($month) && strlen($month) <= 2 && ($month > 0 && $month <= 12);
	}

	/**
	 * 参数类型是否为日期的日格式（1-31）
	 *
	 * @param int|string $day
	 * @return bool
	 */
	public static function day($day)
	{
	    return is_numeric($day) && strlen($day) <= 2 && ($day> 0 && $day<= 31);
	}

	/**
	 * 参数类型是否为时间的小时格式（0-23）
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function hour($hour)
	{
	    return is_numeric($hour) && strlen($hour) <= 2 && $hour >= 0 && $hour <= 23;
	}

	/**
	 * 参数类型是否为时间的分钟格式（0-59）
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function minute($var)
	{
	    return is_numeric($var) && strlen($var) <= 2 && $var >= 0 && $var < 60;
	}

	/**
	 * 参数类型是否为时间的秒钟格式（0-59）
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function second($var)
	{
		return static::minute($var);
	}

	/**
	 * 是否为星期范围内的值
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function week($var)
	{
		$weeks = [
		    1, 2, 3, 4, 5, 6, 7, 
		    '１', '２', '３', '４', '５', '６', '７', 
		    '一', '二', '三', '四', '五', '六', '天', '日', 
		    'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday',
		    'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'
		];
		$var = strtolower($var);
		
		return in_array($var, $weeks);
	}

	/**
	 * 参数类型是否为十六进制字符串
	 *
	 * @param int|string $var
	 * @return bool
	 */
	public static function hex($var)
	{
		return (bool)preg_match('/^[0-9A-Fa-f]*$/', ltrim($var, '-'));
	}

	/**
	 * 身份证号码
	 * 可以验证15和18位的身份证号码
	 *
	 * @param string $var
	 * @return bool
	 */
	public static function idCard($var)
	{
		$province = [
		    "11", "12", "13", "14", "15", 
		    "21", "22", "23", 
		    "31", "32", "33", "34", "35", "36", "37", 
		    "41", "42", "43", "44", "45", "46", 
		    "50", "51", "52", "53", "54", 
		    "61", "62", "63", "64", "65", 
		    "71", "81", "82", "91"
		];
		//前两位的省级代码
		if(!in_array(substr($var, 0, 2), $province)) {
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
			if(!@checkdate(substr($var, 10, 2), substr($var, 12, 2), substr($var, 6, 4))) {
				return false;
			}
			
			//加权因子Wi=2^（i-1）(mod 11)计算得出
			$Wi_arr = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
			
			//校验码对应值
			$VN_arr = [1, 0, 'x', 9, 8, 7, 6, 5, 4, 3, 2];
			
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
	public static function utf8($text)
	{
		if(strlen($text) == 0) {
			return true;
		}
		
		return (preg_match('/^./us', $text) == 1);
	}
	
	/**
	 * 检查日期格式是否正确
	 *
	 * @param string $text 日期，如：2000-01-20 或 2017/1/8
	 */
	public static function date($text)
	{
	    if(!preg_match("/^([\\d]+)[\\-\\/]([\\d]{1,2})[\\-\\/]([\\d]{1,2})$/", $text, $match)) {
	        return false;
	    }
	    
	    list($_, $year, $month, $day) = $match;
	    
	    // 001
	    if (strlen($month) > 2 || strlen($day) > 2) {
	        return false;
	    }
	    
	    if (!checkdate($month, $day, $year)) {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * 检查时间格式（YYYY-mm-dd HH:ii:ss）是否正确
	 *
	 * @param string $text 时间，如：2011-01-20 20:00:20
	 */
	public static function datetime($text)
	{
	    print $text . "\n";
	    if(!preg_match("/^([\\d]+)[\\-\\/]([\\d]{1,2})[\\-\\/]([\\d]{1,2})\\s+([0-2]{1,2}):([\\d]{1,2}):([\\d]{1,2})$/", $text, $match)) {
	        return false;
	    }
	    print_r($match);
	    list($_, $year, $month, $day, $hour, $minute, $second) = $match;
	    if (strlen($month) > 2 || strlen($day) > 2) {
	        return false;
	    }
	    
	    // 001
	    if (strlen($month) > 2 || strlen($day) > 2 || strlen($hour) > 2 || strlen($minute) > 2 || strlen($second) > 2) {
	        return false;
	    }
	    
	    // 时 0-23，分/秒 0-59
	    if($hour >= 24 || $minute >= 60 || $second >= 60) {
	        return false;
	    }
	    
	    if (!checkdate($month, $day, $year)) {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * 是否是手机号
	 * 
	 * @param number $mobile
	 * @return bool
	 */
	public static function mobile($mobile)
	{
		return (bool)preg_match("/^1[34578]{1}[0-9]{9}$/", $mobile);
	}
	
	/**
	 * 字符串长度不超过
	 * @param string $text
	 * @param number|array $maxLen
	 */
	public static function maxLen($text, $maxLen)
	{
	    if (is_array($maxLen)) {
	        if (!isset($maxLen['maxLen'])) {
	            throw new \InvalidArgumentException('The $minLen argument must have "maxLen" key');
	        }
	        $maxLen = $maxLen['maxLen'];
	    }
	    
	    return strlen($text) <= $maxLen;
	}
	
	/**
	 * 字符串长度不小于
	 * @param string $text
	 * @param number|array $minLen
	 */
	public static function minLen($text, $minLen)
	{
	    if (is_array($minLen)) {
	        if (!isset($minLen['minLen'])) {
	            throw new \InvalidArgumentException('The $minLen argument must have "minLen" key');
	        }
	        $minLen = $minLen['minLen'];
	    }
	    
	    return strlen($text) >= $minLen;
	}
	
	/**
	 * 字符串长度等于
	 * @param string $text
	 * @param number|array $len
	 */
	public static function len($text, $len)
	{
	    if (is_array($len)) {
	        if (!isset($len['len'])) {
	            throw new \InvalidArgumentException('The $len argument must have "len" key');
	        }
	        $len = $len['minLen'];
	    }
	    
	    return strlen($text) == $len;
	}
	
	/**
	 * 值等于（==）
	 * @param string $text
	 * @param number|array $expect
	 */
	public static function equal($text, $expect)
	{
	    if (is_array($expect)) {
	        if (!isset($expect['expect'])) {
	            throw new \InvalidArgumentException('The $expect argument must have "expect" key');
	        }
	        $expect = $expect['expect'];
	    }
	    
	    return $text == $expect;
	}
	
	
	/**
	 * 值全等于（===）
	 * @param string $text
	 * @param number|array $expect
	 */
	public static function equalAll($text, $expect)
	{
	    if (is_array($expect)) {
	        if (!isset($expect['expect'])) {
	            throw new \InvalidArgumentException('The $expect argument must have "expect" key');
	        }
	        $expect = $expect['expect'];
	    }
	    
	    return $text === $expect;
	}
	
	/**
	 * 值不大于
	 * @param number $val
	 * @param number|array $max
	 */
	public static function max($val, $max)
	{
	    if (is_array($max)) {
	        if (!isset($max['max'])) {
	            throw new \InvalidArgumentException('The $max argument must have "max" key');
	        }
	        $max = $max['max'];
	    }
	    
	    return $val <= $max;
	}
	
	/**
	 * 值不小于
	 * @param number $val
	 * @param number|array $min
	 */
	public static function min($val, $min)
	{
	    if (is_array($min)) {
	        if (!isset($min['min'])) {
	            throw new \InvalidArgumentException('The $min argument must have "min" key');
	        }
	        $min= $min['min'];
	    }
	    
	    return $val >= $min;
	}
	
	/**
	 * 自定义正则匹配规则
	 * @param string $text
	 * @param string|array $preg 正则匹配规则
	 * @return boolean
	 */
	public static function preg($text, $preg)
	{
	    if (!$preg) {
	        throw new \InvalidArgumentException('Please set the $preg pattern');
	    }
	    
	    if (is_array($preg)) {
	        if (empty($preg['preg'])) {
	            throw new \InvalidArgumentException('The $preg argument must have "preg" key');
	        }
	        $preg = $preg['preg'];
	    }
	    
	    return (bool)preg_match($preg, $text);
	}
		
}
