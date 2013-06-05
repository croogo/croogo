<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class CroogoTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('Croogo Tests');
		$path = TESTS . 'Case' . DS;
		$suite->addTestFile($path . 'CroogoModelsTest.php');
		$suite->addTestFile($path . 'CroogoBehaviorsTest.php');
		$suite->addTestFile($path . 'CroogoHelpersTest.php');
		$suite->addTestFile($path . 'CroogoControllersTest.php');
		$suite->addTestFile($path . 'CroogoComponentsTest.php');
		$suite->addTestFile($path . 'CroogoEventsTest.php');
		$suite->addTestFile($path . 'CroogoLibsTest.php');
		$suite->addTestFile($path . 'CroogoConsolesTest.php');
		$suite->addTestFile($path . 'CroogoCorePluginsTest.php');
		return $suite;
	}

}
