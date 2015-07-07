<?php
namespace Croogo\Nodes\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class AllNodesTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Nodes tests');
		$suite->addTestDirectoryRecursive(Plugin::path('Nodes') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
