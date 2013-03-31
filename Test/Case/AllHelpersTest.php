<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllHelpersTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All helper tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
