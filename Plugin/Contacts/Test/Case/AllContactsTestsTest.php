<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllContactsTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Contacts tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Contacts') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
