<?php
namespace Croogo\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class CroogoConsolesTests extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo commands tests');
		$path = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Console' . DS . 'Command' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
