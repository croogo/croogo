<?php

class AllAclComponentsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Acl component class tests');
		$path = CakePlugin::path('Acl') . DS . 'Test' . DS . 'Case';
		$suite->addTestDirectory($path . DS . 'Controller' . DS . 'Component');
		return $suite;
	}

}
