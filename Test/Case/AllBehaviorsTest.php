<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllBehaviorsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All behavior tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'Model' . DS . 'Behavior' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
