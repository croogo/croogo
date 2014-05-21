<?php
namespace Croogo\Test\TestCase;
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoComponentsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 */
	public static function suite() {
		$suite = new CakeTestSuite('Croogo components tests');
		$path = APP . 'Vendor' . DS . 'croogo' . DS . 'croogo' . DS . 'Croogo' . DS . 'Test' . DS . 'Case' . DS . 'Controller' . DS . 'Component' . DS;
		$suite->addTestDirectory($path);
		return $suite;
	}

}
