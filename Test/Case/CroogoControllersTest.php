<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoControllerTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo controller tests');
		$path = APP . DS . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Controller' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
