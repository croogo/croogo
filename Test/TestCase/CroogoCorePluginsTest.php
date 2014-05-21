<?php
namespace Croogo\Test\TestCase;

use Croogo\TestSuite\CroogoTestCase;
/**
 *  CroogoCorePluginsTest
 *
 */
class CroogoCorePluginsTest extends PHPUnit_Framework_TestSuite {

	public static function suite() {
		$suite = new CakeTestSuite('Croogo core plugins tests');
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
		if ((integer)Configure::read('debug') > 0) {
			$plugins[] = 'Install';
		}
		foreach ($plugins as $plugin) {
			Plugin::load($plugin);
			$suite->addTestDirectoryRecursive(Plugin::path($plugin) . 'Test' . DS);
		}
		return $suite;
	}

}
