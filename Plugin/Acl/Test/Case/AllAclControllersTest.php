<?php

class AllAclControllersTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Acl controller class tests');
		$path = CakePlugin::path('Acl') . DS . 'Test' . DS . 'Case';
		$suite->addTestDirectory($path . DS . 'Controller');
		return $suite;
	}

}
