<?php
namespace Croogo\Taxonomy\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class AllTaxonomyTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Taxonomy tests');
		$suite->addTestDirectoryRecursive(Plugin::path('Taxonomy') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
