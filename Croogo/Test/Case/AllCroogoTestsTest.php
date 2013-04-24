<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllCroogoTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Croogo tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Croogo') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
