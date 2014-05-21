<?php
namespace Croogo\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class CroogoBehaviorsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo behavior tests');
		$path = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Model' . DS . 'Behavior' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}
}
