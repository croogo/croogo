<?php
App::uses('CroogoTestCase', 'TestSuite');

/**
 *  AllCorePluginsTest
 *
 */
class AllCorePluginsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All core plugins tests');
		$path = CakePlugin::path('Acl') . 'Test' . DS . 'Case' . DS;
		$suite->addTestFile($path . 'AllAclTestsTest.php');
		$path = CakePlugin::path('Extensions') . DS . 'Test' . DS . 'Case' . DS;
		$suite->addTestFile($path . 'AllExtensionsTestsTest.php');
		return $suite;
	}

}
