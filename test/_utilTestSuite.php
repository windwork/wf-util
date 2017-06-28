<?php

/**
 * Static test suite.
 */
class _utilTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName('testSuite');
		$this->addTestFile('StrTest.php');
		$this->addTestFile('EncoderTest.php');
		$this->addTestFile('ValidatorTest.php');
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self();
	}
}

