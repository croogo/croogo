<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllModelsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All model tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'Model' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
