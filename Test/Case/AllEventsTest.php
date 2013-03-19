<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllEventsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All events tests');
		$path = APP . 'Test' . DS . 'Case' . DS . 'Event' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}