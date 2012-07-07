<?php

class AllExtensionsTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All Extensions plugin tests');
		$path = CakePlugin::path('Extensions') . DS . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectory($path . 'Lib');
		return $suite;
	}

}
