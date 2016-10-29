<?php
require_once '../lib/Encoder.php';

use wf\util\Encoder;

/**
 * Encoder test case.
 */
class EncoderTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Encoder
	 */
	private $encoder;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated EncoderTest::setUp()
		
		$this->encoder = new Encoder(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated EncoderTest::tearDown()
		$this->encoder = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Encoder::encode()
	 */
	public function testEncode() {
		$str = 'sd<a href="xxx">ds</a>f 大赛……\'"ssdfdoi ijo sdfsdfoJIds 决斗你是否 joi';
		
		$encode = Encoder::encode($str);
		$decode = Encoder::decode($encode);
		
		$this->assertEquals($str, $decode);		
	}
	
	/**
	 * Tests Encoder::decode()
	 */
	public function testDecode() {
		$str = <<<TEXT
 <div id="oop5.intro" class="sect1">
  <h2 class="title">简介<a class="genanchor" href="#oop5.intro"> ¶</a></h2>
  <p class="para">
   自 PHP 5 起完全重写了对象模型以得到更佳性能和更多特性。这是自 PHP 4
   以来的最大变化。PHP 5 具有完整的对象模型。
  </p>

  <p class="para">
   PHP 5 中的新特性包括<a href="language.oop5.visibility.php" class="link">访问控制</a>，<a href="language.oop5.abstract.php" class="link">抽象类</a>和 <a href="language.oop5.final.php" class="link">final</a> 类与方法，附加的<a href="language.oop5.magic.php" class="link">魔术方法</a>，<a href="language.oop5.interfaces.php" class="link">接口</a>，<a href="language.oop5.cloning.php" class="link">对象复制</a>和<a href="language.oop5.typehinting.php" class="link">类型约束</a>。
  </p>
  <p class="para">  
   PHP 对待对象的方式与引用和句柄相同，即每个变量都持有对象的引用，而不是整个对象的拷贝。参见<a href="language.oop5.references.php" class="link">对象和引用</a>。
  </p>
  <div class="tip"><strong class="tip">Tip</strong><p class="simpara">请参见<a href="userlandnaming.php" class="xref">用户空间命名指南</a>。</p></div>
 </div>
TEXT;
		
		$encode = Encoder::encode($str);
		$decode = Encoder::decode($encode);
		
		$this->assertEquals($str, $decode);	
	}
}

