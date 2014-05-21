<?php
namespace Croogo\Settings\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
class AllSettingsTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Settings tests');
		$suite->addTestDirectoryRecursive(Plugin::path('Settings') . 'Test' . DS . 'Case' . DS);
		return $suite;
	}

}
