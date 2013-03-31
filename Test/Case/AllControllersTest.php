<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllControllerTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All controller tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'Controller' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
