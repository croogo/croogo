<?php

class AllAclTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Acl plugin tests');
		$path = CakePlugin::path('Acl') . DS . 'Test' . DS . 'Case' . DS;
		$suite->addTestFile($path . 'AllAclControllersTest.php');
		$suite->addTestFile($path . 'AllAclComponentsTest.php');
		return $suite;
	}

}
