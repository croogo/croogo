<?php

class AllAclTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Acl plugin tests');
		$suite->addTestDirectoryRecursive(CakePlugin::path('Acl') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
