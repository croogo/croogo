<?php
App::uses('CroogoTestCase', 'Croogo.TestSuite');

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
			'Comments',
			'Contacts',
			'Croogo',
			'Nodes',
			'Extensions',
			'FileManager',
			'Menus',
			'Meta',
			'Settings',
			'Taxonomy',
			'Ckeditor',
			'Translate',
			'Users',
		);
		foreach ($plugins as $plugin) {
			CakePlugin::load($plugin);
			$suite->addTestDirectoryRecursive(CakePlugin::path($plugin) . 'Test' . DS);
		}
		return $suite;
	}

}
