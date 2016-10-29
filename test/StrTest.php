<?php
require_once '../lib/Str.php';

use \wf\util\Str;

/**
 * Str test case.
 */
class StrTest extends PHPUnit_Framework_TestCase {
	
	/**
	 *
	 * @var Str
	 */
	private $str;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated StrTest::setUp()
		
		$this->str = new Str(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated StrTest::tearDown()
		$this->str = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests Str::ltrim()
	 */
	public function testLtrim() {
		$str = 'xyxx-dsdsjioabcedfx';
		$trm = Str::ltrim($str, 'xyxx-');
		
		$this->assertEquals('dsdsjioabcedfx', $trm);
		

		$trm = Str::ltrim($str, 'xyxx^');
		$this->assertEquals($str, $trm);
	}
	
	/**
	 * Tests Str::rtrim()
	 */
	public function testRtrim() {
		$str = 'abcedfx';
		$trm = Str::rtrim($str, 'dfx');
		
		$this->assertEquals('abce', $trm);
		

		$trm = Str::rtrim($str, 'd-fx');
		$this->assertEquals($str, $trm);
	}
	
	/**
	 * Tests Str::trim()
	 */
	public function testTrim() {
		$str1 = "aabbxxxccbbbb";
		$trim = Str::trim($str1, 'aabb');
		
		$this->assertEquals('xxxccbbbb', $trim);

		$str1 = "aabbxxxccbbaabb";
		$trim = Str::trim($str1, 'aabb');
		
		$this->assertEquals('xxxccbb', $trim);
	}
	
	/**
	 * Tests Str::toSemiangle()
	 */
	public function testToSemiangle() {
		// TODO Auto-generated StrTest::testToSemiangle()
		$this->markTestIncomplete ( "toSemiangle test not implemented" );
		
		Str::toSemiangle(/* parameters */);
	}
	
	/**
	 * Tests Str::htmlspecialcharsDeep()
	 */
	public function testHtmlspecialcharsDeep() {
		// TODO Auto-generated StrTest::testHtmlspecialcharsDeep()
		$this->markTestIncomplete ( "htmlspecialcharsDeep test not implemented" );
		
		Str::htmlspecialcharsDeep(/* parameters */);
	}
	
	/**
	 * Tests Str::htmlTagReplace()
	 */
	public function testHtmlTagReplace() {
		// TODO Auto-generated StrTest::testHtmlTagReplace()
		$this->markTestIncomplete ( "htmlTagReplace test not implemented" );
		
		Str::htmlTagReplace(/* parameters */);
	}
	
	/**
	 * Tests Str::randNum()
	 */
	public function testRandNum() {
		// TODO Auto-generated StrTest::testRandNum()
		$this->markTestIncomplete ( "randNum test not implemented" );
		
		Str::randNum(/* parameters */);
	}
	
	/**
	 * Tests Str::randStr()
	 */
	public function testRandStr() {
		// TODO Auto-generated StrTest::testRandStr()
		$this->markTestIncomplete ( "randStr test not implemented" );
		
		Str::randStr(/* parameters */);
	}
	
	/**
	 * Tests Str::guid()
	 */
	public function testGuid() {
		// TODO Auto-generated StrTest::testGuid()
		$this->markTestIncomplete ( "guid test not implemented" );
		
		Str::guid(/* parameters */);
	}
	
	/**
	 * Tests Str::addAnchor()
	 */
	public function testAddAnchor() {
		// TODO Auto-generated StrTest::testAddAnchor()
		$this->markTestIncomplete ( "addAnchor test not implemented" );
		
		Str::addAnchor(/* parameters */);
	}
	
	/**
	 * Tests Str::stripSlug()
	 */
	public function testStripSlug() {
		// TODO Auto-generated StrTest::testStripSlug()
		$this->markTestIncomplete ( "stripSlug test not implemented" );
		
		Str::stripSlug(/* parameters */);
	}
	
	/**
	 * Tests Str::stripDesc()
	 */
	public function testStripDesc() {
		// TODO Auto-generated StrTest::testStripDesc()
		$this->markTestIncomplete ( "stripDesc test not implemented" );
		
		Str::stripDesc(/* parameters */);
	}
	
	/**
	 * Tests Str::gdTextWrap()
	 */
	public function testGdTextWrap() {
		// TODO Auto-generated StrTest::testGdTextWrap()
		$this->markTestIncomplete ( "gdTextWrap test not implemented" );
		
		Str::gdTextWrap(/* parameters */);
	}
	
	/**
	 * Tests Str::stripTags()
	 */
	public function testStripTags() {
		// TODO Auto-generated StrTest::testStripTags()
		$this->markTestIncomplete ( "stripTags test not implemented" );
		
		Str::stripTags(/* parameters */);
	}
	
	/**
	 * Tests Str::keywordsFormat()
	 */
	public function testKeywordsFormat() {
		// TODO Auto-generated StrTest::testKeywordsFormat()
		$this->markTestIncomplete ( "keywordsFormat test not implemented" );
		
		Str::keywordsFormat(/* parameters */);
	}
	
	/**
	 * Tests Str::addLazyLoadToContentImage()
	 */
	public function testAddLazyLoadToContentImage() {
		// TODO Auto-generated StrTest::testAddLazyLoadToContentImage()
		$this->markTestIncomplete ( "addLazyLoadToContentImage test not implemented" );
		
		Str::addLazyLoadToContentImage(/* parameters */);
	}
	
	/**
	 * Tests Str::stripTagsDeep()
	 */
	public function testStripTagsDeep() {
		// TODO Auto-generated StrTest::testStripTagsDeep()
		$this->markTestIncomplete ( "stripTagsDeep test not implemented" );
		
		Str::stripTagsDeep(/* parameters */);
	}
	
	/**
	 * Tests Str::shortUrl()
	 */
	public function testShortUrl() {
		// TODO Auto-generated StrTest::testShortUrl()
		$this->markTestIncomplete ( "shortUrl test not implemented" );
		
		Str::shortUrl(/* parameters */);
	}
	
	/**
	 * Tests Str::htmlAttr()
	 */
	public function testHtmlAttr() {
		// TODO Auto-generated StrTest::testHtmlAttr()
		$this->markTestIncomplete ( "htmlAttr test not implemented" );
		
		Str::htmlAttr(/* parameters */);
	}
	
	/**
	 * Tests Str::pw()
	 */
	public function testPw() {
		// TODO Auto-generated StrTest::testPw()
		$this->markTestIncomplete ( "pw test not implemented" );
		
		Str::pw(/* parameters */);
	}
	
	/**
	 * Tests Str::safeString()
	 */
	public function testSafeString() {
		// TODO Auto-generated StrTest::testSafeString()
		$this->markTestIncomplete ( "safeString test not implemented" );
		
		Str::safeString(/* parameters */);
	}
	
	/**
	 * Tests Str::isEqual()
	 */
	public function testIsEqual() {
		// TODO Auto-generated StrTest::testIsEqual()
		$this->markTestIncomplete ( "isEqual test not implemented" );
		
		Str::isEqual(/* parameters */);
	}
}

