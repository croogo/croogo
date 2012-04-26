<?php
App::uses('CroogoTestCase', 'TestSuite');

/**
 *  AllCorePluginsTest
 *
 */
class AllCorePluginsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All core plugins tests');
		$path = APP . 'Plugin' . DS . 'Acl' . DS . 'Test' . DS . 'Case' . DS;
		$suite->addTestFile($path . 'AllAclTestsTest.php');
		return $suite;
	}

}
