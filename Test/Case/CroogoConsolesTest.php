<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoConsolesTests extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo commands tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'Console' . DS . 'Command' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
