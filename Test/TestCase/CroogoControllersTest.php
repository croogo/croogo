<?php
namespace Croogo\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class CroogoControllerTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo controller tests');
		$path = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Controller' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
