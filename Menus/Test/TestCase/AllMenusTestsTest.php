<?php
namespace Croogo\Menus\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class AllMenusTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Menus tests');
		$suite->addTestDirectoryRecursive(Plugin::path('Menus') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
