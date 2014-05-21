<?php
namespace Croogo\Contacts\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class AllContactsTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Contacts tests');
		$suite->addTestDirectoryRecursive(Plugin::path('Contacts') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
