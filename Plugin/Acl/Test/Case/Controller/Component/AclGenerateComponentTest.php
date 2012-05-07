<?php

App::uses('Controller', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class AclGenerateTestController extends Controller {

	public $components = array('Acl.AclGenerate');

}

class AclGenerateComponentTest extends CroogoTestCase {

	protected $_coreControllers = array(
		'Attachments', 'Blocks', 'Comments', 'Contacts', 'Filemanager',
		'Languages', 'Links', 'Menus', 'Messages', 'Nodes', 'Regions',
		'Roles', 'Settings', 'Terms', 'Types', 'Users', 'Vocabularies',
		);

	protected $_extensionsControllers = array(
		'ExtensionsLocales', 'ExtensionsPlugins', 'ExtensionsThemes',
		);

	public function setUp() {
		$this->Controller = new AclGenerateTestController(new CakeRequest(), new CakeResponse());
		$this->Controller->constructClasses();
		$this->Controller->startupProcess();
	}

	public function testListControllers() {
		$controllerPath = $this->Controller->AclGenerate->listControllers();
		$controllers = array_keys($controllerPath);

		$this->assertFalse(in_array('CakeError', $controllers));

		$result = array_intersect($this->_coreControllers, $controllers);
		$this->assertEquals($this->_coreControllers, $result);

		$result = array_intersect($this->_extensionsControllers, $controllers);
		$this->assertEquals($this->_extensionsControllers, $result);
	}

	public function testListActions() {
		$expected = array(
			'admin_index', 'admin_create', 'admin_add', 'admin_edit',
			'admin_update_paths', 'admin_delete', 'admin_add_meta',
			'admin_delete_meta', 'admin_process', 'index', 'term', 'promoted',
			'search', 'view',
			);
		$result = $this->Controller->AclGenerate->listActions('Nodes', APP . DS . 'Controller');
		sort($result);
		sort($expected);
		$this->assertEquals($expected, $result);
	}

}
