<?php
App::uses('CroogoTestCase', 'TestSuite');

class AllTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Taxonomy') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
