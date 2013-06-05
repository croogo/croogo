<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoHelpersTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo helper tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
