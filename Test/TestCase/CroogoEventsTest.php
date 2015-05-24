<?php
namespace Croogo\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class CroogoEventsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo events tests');
		$path = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Event' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
