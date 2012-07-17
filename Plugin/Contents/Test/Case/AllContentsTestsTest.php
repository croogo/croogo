<?php
App::uses('CroogoTestCase', 'TestSuite');

class AllContentsTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Contents tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Contents') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
