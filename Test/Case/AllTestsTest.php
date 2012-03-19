<?php
class AllTestsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All controller tests');
		$path = APP . 'Test' .DS. 'Case' .DS;
		$suite->addTestFile($path . 'AllModelsTest.php');
		$suite->addTestFile($path . 'AllBehaviorsTest.php');
		$suite->addTestFile($path . 'AllHelpersTest.php');
		$suite->addTestFile($path . 'AllControllersTest.php');
		$suite->addTestFile($path . 'AllComponentsTest.php');
		$suite->addTestFile($path . 'AllLibsTest.php');

		$path = APP . 'Plugin' .DS. 'Acl' .DS. 'Test' .DS. 'Case' .DS;
		$suite->addTestFile($path . 'AllAclTestsTest.php');
		return $suite;
	}
}
