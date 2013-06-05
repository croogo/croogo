<?php

class AllUsersTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Users plugin tests');
		$path = CakePlugin::path('Users') . DS . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectory($path . 'Controller');
		$suite->addTestDirectory($path . 'Model');
		return $suite;
	}

}
