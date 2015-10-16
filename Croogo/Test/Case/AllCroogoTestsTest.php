<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

ini_set('error_reporting', ini_get('error_reporting') & ~E_USER_DEPRECATED);

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
