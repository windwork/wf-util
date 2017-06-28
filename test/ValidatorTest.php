<?php
require_once '../lib/Validator.php';

use wf\util\Validator;
/**
 * Validator test case.
 */
class ValidatorTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Validator
	 */
	private $validator;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated ValidatorTest::setUp()
		
		$this->validator = new Validator();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated ValidatorTest::tearDown()
		$this->validator = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Validator::validErr()
	 */
	public function testValidate() {
		$rules = [
		    'user_name' => [
		        // 验证方法只有一个参数
		        'required'   => '请输入用户名', 
		        'safeString' => '用户名只允许输入字母、数字和下划线',
		
		        // 验证方法需要多个参数
		        'minLen'     => ['msg' => '用户名不能小于3个字符', 'minLen' => 3],
		        'maxLen'     => ['msg' => '用户名不能超过20个字符', 'maxLen' => 20],
		    ],
		    'email'    => [
		        'required'   => '请输入邮箱', 
		        'email'      => '邮箱格式错误',
		    ]
		];		

		// ok
		$data = [
			'user_name' => 'cmpan',
			'email' => 'cmpan@qq.com',
			'xxx' => '',
			'yyy' => '',
		];
		$obj = new Validator();
		$obj->validate($data, $rules);
		$errs = $obj->getErrors();
		$this->assertEmpty($errs);
		
		// empty
		$data = [
			'user_name' => '',
			'email' => '',
		];
		$obj->validate($data, $rules);
		$errs = $obj->getErrors();
		$this->assertNotEmpty($errs);
				
		// minLen
		$data = [
			'user_name' => 'aa',
			'email' => 'cmpan@qq.com',
		];
		$obj->validate($data, $rules);
		$errs = $obj->getErrors();
		$this->assertNotEmpty($errs);
		
		// maxLen
		$data = [
			'user_name' => '12345678901234567890xx',
			'email' => 'cmpan@qq.com',
		];
		$obj->validate($data, $rules);
		$errs = $obj->getErrors();
		$this->assertNotEmpty($errs);
		
		// safe string
		$data = [
			'user_name' => 'aaa^',
			'email' => 'cmpan@qq.com',
		];
		$obj->validate($data, $rules);
		$errs = $obj->getErrors();
		$this->assertNotEmpty($errs);
		
		// email
		$data = [
			'user_name' => 'aaaa',
			'email' => 'cmpan.qq.com',
		];
		$obj->validate($data, $rules);
		$errs = $obj->getErrors();
		$this->assertNotEmpty($errs);
	}
	
	/**
	 * Tests Validator::email()
	 */
	public function testEmail() {
		$this->assertTrue(Validator::email('cmpan@qq.com'));
		$this->assertFalse(Validator::email('cmpan'));
	}
	
	/**
	 * Tests Validator::time()
	 */
	public function testDatetime() {
	    $this->assertTrue(Validator::datetime('2014-02-21 10:54:58'));
	    $this->assertTrue(Validator::datetime('2014/02/21 10:54:58'));
	    $this->assertTrue(Validator::datetime('2014-02-1 10:54:58'));
	    $this->assertTrue(Validator::datetime('2014-2-01 10:54:58'));
	    $this->assertTrue(Validator::datetime('2014-2-01 0:0:0'));
	    
	    $this->assertFalse(Validator::datetime('dsf-x-d H:i:s'));
	    $this->assertFalse(Validator::datetime('99999-01-01 00:00:00')); // 年
	    $this->assertFalse(Validator::datetime('2000-13-01 00:00:00')); // 月
	    $this->assertFalse(Validator::datetime('2000-001-01 00:00:00'));
	    $this->assertFalse(Validator::datetime('2000-01-32 00:00:00')); // 日
	    $this->assertFalse(Validator::datetime('2000-01-001 00:00:00'));
	    $this->assertFalse(Validator::datetime('2000-01-01 24:00:00')); // 时
	    $this->assertFalse(Validator::datetime('2000-01-01 000:00:00'));
	    $this->assertFalse(Validator::datetime('2000-01-01 00:60:00')); // 分
	    $this->assertFalse(Validator::datetime('2000-01-01 00:000:00'));
	    $this->assertFalse(Validator::datetime('2000-01-01 00:00:60')); // 秒
	    $this->assertFalse(Validator::datetime('2000-01-01 00:00:000')); 
	}
	
	/**
	 * Tests Validator::required()
	 */
	public function testRequired() {
		$this->assertTrue(Validator::required('ss'));
		$this->assertFalse(Validator::required(''));
	}
	
	/**
	 * Tests Validator::safeString()
	 */
	public function testSafeString() {
		$this->assertTrue(Validator::safeString('absdsdJh223_ds'));
		$this->assertNotTrue(Validator::safeString('absdsd\'Jh223_ds'));
	}
	
	/**
	 * Tests Validator::money()
	 */
	public function testIsMoney() {
		$this->assertTrue(Validator::money(11.23));
		$this->assertNotTrue(Validator::money(115));
		$this->assertNotTrue(Validator::money(0xfff));
		$this->assertNotTrue(Validator::money('s15235'));
		$this->assertNotTrue(Validator::money('$%^&*'));
	}
	
	/**
	 * Tests Validator::isIP()
	 */
	public function testIp() {		
		$this->assertTrue(Validator::ip('210.36.168.33'));
		$this->assertTrue(Validator::ip('127.0.0.1'));
		$this->assertNotTrue(Validator::ip('aa.36.168.33'));
		$this->assertNotTrue(Validator::ip('12563322'));
		$this->assertNotTrue(Validator::ip('abdsds'));
	}
	
	/**
	 * Tests Validator::url()
	 */
	public function testUrl() {
	    $this->assertTrue(Validator::url('http://www.my.com'));
		$this->assertTrue(Validator::url('http://my.net'));
		$this->assertTrue(Validator::url('http://my.com/'));
		$this->assertTrue(Validator::url('http://my.com/sdfdf'));
		$this->assertTrue(Validator::url('https://my.com/sdfdf'));
		$this->assertTrue(Validator::url('https://xx.xx.cc/xx/xx/xx'));
		$this->assertTrue(Validator::url('ftp://my.com/my.txt'));
		$this->assertTrue(Validator::url('ftps://my.com/yy/zz/my.txt'));
		$this->assertTrue(Validator::url('http://my.cc:80'));
		$this->assertTrue(Validator::url('http://www.my.com:80'));
		$this->assertTrue(Validator::url('http://www.my.com:80/path/to/x.html'));
		$this->assertTrue(Validator::url('http://www.my.com:80/path/to/x.html#sdfw'));
		$this->assertTrue(Validator::url('http://www.my.com:80/path/to/x.html?abc=ds&dsf=23#dsfsd'));
		$this->assertTrue(Validator::url('http://www.my.com:80/允许有中文'));
		$this->assertNotTrue(Validator::url('http://www.my.com:80/path/to/x.html sdsd'));// 不允许空格
		$this->assertNotTrue(Validator::url('http://mycom/sdfdf'));
		$this->assertNotTrue(Validator::url('http://mycom/my.net'));
		$this->assertNotTrue(Validator::url('d:\dev\web\demo'));
	}
	
	/**
	 * Tests Validator::number()
	 */
	public function testNumber() {
		$this->assertTrue(Validator::number(1568));
		$this->assertTrue(Validator::number('123458'));
		$this->assertTrue(Validator::number('12358695452325686555555551235869545232568655555555'));
		$this->assertNotTrue(Validator::number('a123458'));
		$this->assertNotTrue(Validator::number('str'));
		$this->assertNotTrue(Validator::number('@#$%^&*('));
	}
	
	/**
	 * Tests Validator::year()
	 */
	public function testYear() {
		$this->assertTrue(Validator::year('1920'));
		$this->assertTrue(Validator::year(2012));
		$this->assertTrue(Validator::year(299));
		$this->assertTrue(Validator::year('10'));
		$this->assertTrue(Validator::year('3356'));
		$this->assertNotTrue(Validator::year('999999'));
		$this->assertNotTrue(Validator::year('dsfdsfsf'));
	}
	
	/**
	 * Tests Validator::month()
	 */
	public function testMonth() {
		$this->assertTrue(Validator::month(12));
		$this->assertTrue(Validator::month(1));
		$this->assertTrue(Validator::month('02'));
		
		$this->assertNotTrue(Validator::month('002'));
		$this->assertNotTrue(Validator::month(20));
		$this->assertNotTrue(Validator::month('%^&*'));
		$this->assertNotTrue(Validator::month(98));
	}
	
	/**
	 * Tests Validator::day()
	 */
	public function testDay() {
		$this->assertTrue(Validator::day(12));
		$this->assertTrue(Validator::day(1));
		$this->assertTrue(Validator::day('05'));
		$this->assertTrue(Validator::day(31));
		

		$this->assertNotTrue(Validator::day(32));
		$this->assertNotTrue(Validator::day(-12));
		$this->assertNotTrue(Validator::day('sd'));
		$this->assertNotTrue(Validator::day('#$%^&*('));
	}
	
	/**
	 * Tests Validator::hour()
	 */
	public function testHour() {
		$this->assertTrue(Validator::hour('01'));
		$this->assertTrue(Validator::hour('09'));
		$this->assertTrue(Validator::hour('12'));
		$this->assertTrue(Validator::hour(1));
		$this->assertTrue(Validator::hour(23));
		

		$this->assertNotTrue(Validator::hour(24));
		$this->assertNotTrue(Validator::hour(-1));
		$this->assertNotTrue(Validator::hour('001'));
		$this->assertNotTrue(Validator::hour(33));
		$this->assertNotTrue(Validator::hour('$%^&*'));
		$this->assertNotTrue(Validator::hour('Hgh'));
	}
	
	/**
	 * Tests Validator::minute()
	 */
	public function testMinute() {
		$this->assertTrue(Validator::minute('00'));
		$this->assertTrue(Validator::minute('01'));
		$this->assertTrue(Validator::minute('09'));
		$this->assertTrue(Validator::minute('59'));
		$this->assertTrue(Validator::minute(0));
		$this->assertTrue(Validator::minute(1));
		$this->assertTrue(Validator::minute(59));

		$this->assertNotTrue(Validator::minute('001'));
		$this->assertNotTrue(Validator::minute('-01'));
		$this->assertNotTrue(Validator::minute('sdf'));
		$this->assertNotTrue(Validator::minute(-1));
		$this->assertNotTrue(Validator::minute(60));
	}
	
	/**
	 * Tests Validator::second()
	 */
	public function testSecond() {
		// TODO Auto-generated ValidatorTest::testIsSecond()
		$this->markTestIncomplete ( "same as testMinute()" );
		
		Validator::second(/* parameters */);
	}
	
	/**
	 * Tests Validator::week()
	 */
	public function testWeek() {
		$weeks = [
			1, 2, 3, 4, 5, 6, 7, 
			'１', '２', '３', '４', '５', '６', '７', 
			'一', '二', '三', '四', '五', '六', '天', '日', 
			'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 
			'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
			'Monday', 'Friday', 'Wed', 'THU', 'FRI', 'Sat', 'SUn'				
		];
		
		foreach ($weeks as $week) {
			$this->assertTrue(Validator::week($week));
		}
		
		$falseWeeks = [8, -1, 0, '第三方', 'sdsdsd', '$%'];
		foreach ($falseWeeks as $week) {
			$this->assertNotTrue(Validator::week($week));
		}
	}
	
	/**
	 * Tests Validator::hex()
	 */
	public function testHex() {
		$trueArr = ['fa01', '-ed','af', '01', 45, 0xfff, 99, -99];
		foreach ($trueArr as $hex) {
			$this->assertTrue(Validator::hex($hex));
		}
		
		$falseArr = ['ga00','aw', '^&'];
		foreach ($falseArr as $hex) {
			$this->assertNotTrue(Validator::hex($hex));
		}
		
	}
	
	/**
	 * Tests Validator::idCard()
	 */
	public function testIdCard() {
		$idCards = [
			'431381198109106573',
			'13092119710325337X',
			'360829197705237199',
			'150221197804265512',
			'12011119860622223X',
			'652701198201282897',
			'330622810725323',
			'112221290815224',
			'320521720807022',
		];
		foreach ($idCards as $idCard) {
			$this->assertTrue(Validator::idCard($idCard));
		}

		$idCards = [
			'431381198109106571',
			'13092119710325337X1',
			'360829197705237199y',
			'150221197804265513',
			'12011119860622223Xy',
			'6527011982012828978',
			'112221291815211',
			'320521720837022',
			'320521721307022',
			'32052172080702X',
			'@#$%^&*',
		];
		foreach ($idCards as $idCard) {
			$this->assertNotTrue(Validator::idCard($idCard));
		}
		
	}
	
	/**
	 * Tests Validator::utf8()
	 */
	public function testUtf8() {
		$this->assertTrue(Validator::utf8('解耦'));
		
		$this->assertNotTrue(Validator::utf8(mb_convert_encoding('解耦', 'GBK')));
		$this->assertNotTrue(Validator::utf8(mb_convert_encoding('解耦', 'BIG-5'))); // 繁体
	}
	
	/**
	 * Tests Validator::date()
	 */
	public function testDate() {
		$dates = [
			'2016-01-20',
			'2016-2-02',
			'2016-01-2',
			'1920-9-8',
		];
		foreach ($dates as $date) {
			$this->assertTrue(Validator::date($date));
		}
		
		$dates = [
			'2016-111-2',
			'2016-2-333',
			'99999-01-2',
			'32768-9-8',
		];
		foreach ($dates as $date) {
			$this->assertNotTrue(Validator::date($date));
		}
	}
	
	/**
	 * Tests Validator::mobile()
	 */
	public function testMobile() {
		$mobiles = [
			'13911111111',
			'14911111111',
			'15911111111',
			'17011111111',
			'18911111111',
			'13611111111',
			'18611111111',
		];
		foreach ($mobiles as $mobile) {
			$this->assertTrue(Validator::mobile($mobile));
		}
		
		$mobiles = [
			'12911111111',
			'19911111111',
			'1591111111',
			'170111111111',
			'18911111111x',
			'136111111',
			'1861111111-1',
		];
		foreach ($mobiles as $mobile) {
			$this->assertNotTrue(Validator::mobile($mobile));
		}
	}
	
	/**
	 * Tests Validator::maxLen()
	 */
	public function testMaxLen() {
		$this->assertTrue(Validator::maxLen(333, ['maxLen' => 3]));
		$this->assertTrue(Validator::maxLen(22, ['maxLen' => 2]));
		$this->assertTrue(Validator::maxLen(1, ['maxLen' => 2]));
		$this->assertNotTrue(Validator::maxLen(22, ['maxLen' => 1]));
	}
	
	/**
	 * Tests Validator::minLen()
	 */
	public function testMinLen() {
		$this->assertTrue(Validator::minLen(4444, ['minLen' => 3]));
		$this->assertTrue(Validator::minLen(333, ['minLen' => 2]));
		$this->assertTrue(Validator::minLen(22, ['minLen' => 2]));
		$this->assertNotTrue(Validator::minLen(1, ['minLen' => 2]));
	}
	
	/**
	 * Tests Validator::max()
	 */
	public function testMax() {
		$this->assertTrue(Validator::max(5, ['max' => 10]));
		$this->assertTrue(Validator::max(1, ['max' => 2]));
		$this->assertNotTrue(Validator::max(5, ['max' => 1]));
	}
	
	/**
	 * Tests Validator::min()
	 */
	public function testMin() {
		$this->assertTrue(Validator::min(5, ['min' => 1]));
		$this->assertTrue(Validator::min(2, ['min' => 1]));
		$this->assertNotTrue(Validator::min(0, ['min' => 1]));
	}
}

