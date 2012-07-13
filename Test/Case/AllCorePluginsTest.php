<?php
App::uses('CroogoTestCase', 'TestSuite');

/**
 *  AllCorePluginsTest
 *
 */
class AllCorePluginsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('All core plugins tests');
		$plugins = array(
			'Acl',
			'Blocks',
			'Contacts',
			'Contents',
			'Extensions',
			'FileManager',
			'Menus',
			'Meta',
			'Taxonomy',
			'Tinymce',
			'Users',
		);
		foreach ($plugins as $plugin) {
			CakePlugin::load($plugin);
			$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS);
		}
		return $suite;
	}

}
