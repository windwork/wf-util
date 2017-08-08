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
 * 日期处理
 * 
 * @package     wf.util
 * @author      cm <cmpan@qq.com>
 * @since       0.1.0
 */
class DateTime 
{


	/**
	 * 智能日期显示（把时间显示为n小时n分钟前/后，或昨天、前天、年-月-日后面连着时分秒）
	 * @todo 在\wf\util\DateTime类实现
	 * @param int $time
	 * @param string $ymd 年月日显示格式
	 * @param string $his 时分秒显示格式，空则不显示
	 * @return string
	 */
	function smartDate($time, $ymd = 'Y-m-d', $his = 'H:i:s') 
	{
		$r = '';
	
		if (abs(time() - $time) < 24*3600) {
			$seconds = time() - $time;
			if($seconds > 0) {
				if ($seconds > 3600) {
					$r .= floor($seconds/3600) . "小时";
				}
				$r .= ceil($seconds%3600/60) . "分钟前";
			} else {
				$seconds =  -$seconds;
				if ($seconds > 3600) {
					$r .= floor($seconds/3600) . "小时";
				}
				$r .=  ceil($seconds%3600/60) . "分钟后";
			}
		} else {
			if (date('Y-m-d', strtotime('-1 day')) == date('Y-m-d', $time)) {
				$r = '昨天';
			} elseif (date('Y-m-d', strtotime('-2 day')) == date('Y-m-d', $time)) {
				$r = '前天';
			} elseif (date('Y-m-d', strtotime('+1 day')) == date('Y-m-d', $time)) {
				$r = '明天';
			} elseif (date('Y-m-d', strtotime('+2 day')) == date('Y-m-d', $time)) {
				$r = '后天';
			} else {
				$r = date($ymd, $time);
			}
	
			$his && $r .= ' ' . date($his);
		}
	
		return $r;
	}

}

