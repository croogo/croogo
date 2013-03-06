<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class AllTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All tests');
		$path = APP . 'Test' . DS . 'Case' . DS;
		$suite->addTestFile($path . 'AllModelsTest.php');
		$suite->addTestFile($path . 'AllBehaviorsTest.php');
		$suite->addTestFile($path . 'AllHelpersTest.php');
		$suite->addTestFile($path . 'AllControllersTest.php');
		$suite->addTestFile($path . 'AllComponentsTest.php');
		$suite->addTestFile($path . 'AllEventsTest.php');
		$suite->addTestFile($path . 'AllLibsTest.php');
		$suite->addTestFile($path . 'AllConsolesTest.php');
		$suite->addTestFile($path . 'AllCorePluginsTest.php');
		return $suite;
	}

}
