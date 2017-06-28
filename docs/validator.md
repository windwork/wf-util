字符串格式验证类
======================
本类用于验证字符串格式是否为预期的值。

支持的验证方法：

方法 |  函数说明 
---------------------------| ----------------
Validator::date($str)     | 是否为YYYY-mm-dd或YYY/mm/dd或YYYY年日期
Validator::datetime($str) | 是否为到秒的时间格式（YYYY-mm-dd HH:ii:ss 或 YYYY/mm/dd HH:ii:ss）
Validator::year($str)     | 参数类型是否为年的格式(1-32767)
Validator::month($str)    | 参数类型是否为月格式（1-12）
Validator::day($str)      | 参数类型是否为日期的日格式（1-31）
Validator::hour($str)     | 小时，0-23
Validator::minute($str)   | 分钟，0-59
Validator::second($str)   | 秒钟，0-59
Validator::week($str)     | 否为星期范围内的值
Validator::email($str)    | 邮箱
Validator::equal($str)    | 值等于（==）
Validator::equalAll($str) | 值全等于（===）
Validator::hex($str)      | 是否为16进制字符
Validator::idCard($str)   | 中国身份证号码
Validator::ip($str)          | 是否为IP地址
Validator::max($str, $max)   | 值不大于 $max，如果$max为数组，结构为： <pre> $max = ['max' => 值, 'msg' => '匹配错误提示信息']</pre>
Validator::min($str, $max)   | 值不小于 $min，如果$min为数组，结构为： <pre> $min = ['min' => 值, 'msg' => '匹配错误提示信息']</pre>
Validator::len($str, $len)         | 值长度等于 $len，如果$len为数组，结构为： <pre> $min = ['min' => 值, 'msg' => '匹配错误提示信息']</pre>
Validator::minLen($str, $minLen)   | 值长度不小于 $minLen，如果传入数组，结构为： <pre> $minLen = ['minLen' => 值, 'msg' => '匹配错误提示信息']</pre>
Validator::maxLen($str, $maxLen)   | 值长度不大于 $maxLen，如果传入数组，结构为： <pre> $maxLen = ['maxLen' => 值, 'msg' => '匹配错误提示信息']</pre>
Validator::preg($str, $preg)       | 自定义验证规则，$preg 可直接传入正则，如果传入数组，结构为 <pre> ['preg' => '正则规则', 'msg' => '匹配错误提示信息']</pre>
Validator::mobile($str)      | 中国大陆手机号码
Validator::money($str)       | 货币，两位小数的浮点数，小数点前面必须有数字
Validator::number($str)      | 数字
Validator::safeString($str)  | 安全字符串（只包含字母、数字、下划线）
Validator::url($str)         | 网址


## 批量验证

```
$rules = [
    'user_name' => [
        // 验证方法只有一个参数
        'required'   => '请输入用户名', 
        'safeString' => '用户名只允许输入字母、数字和下划线',

        // 验证方法需要多个参数
        'minLen'       => ['msg' => '用户名不能小于3个字符', 'minLen' => 3],
        'maxLen'       => ['msg' => '用户名不能超过24个字符', 'maxLen' => 24],
    ],
    'email'    => [
        'required'   => '请输入邮箱', 
        'email'        => '邮箱格式错误',
    ]
];
$data = [
    'user_name' => 'my_name_is_han_mei_mei',
    'password'  => '123456',
    'email'     => 'hanmeimei@windwork.org',
];
$validator = new \wf\util\Validator();
if(!$validator->validate($data, $rules)) {
    // 如果匹配错误，获取错误信息并进行处理
    $err = $validator->getLastError();
    // do sth.
}
```
