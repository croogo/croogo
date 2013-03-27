<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllMenusTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Menus tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Menus') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
