<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoLibsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo lib tests');
		$path = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Lib' . DS;
		$suite->addTestDirectory($path);
		$suite->addTestDirectory($path . 'Configure');
		return $suite;
	}

}
