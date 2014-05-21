<?php
namespace Croogo\Croogo\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class AllCroogoTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Croogo tests');
		$suite->addTestDirectoryRecursive(Plugin::path('Croogo') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
