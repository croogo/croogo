<?php
App::uses('FileManagerController', 'FileManager.Controller');
App::uses('CroogoControllerTestCase', 'Croogo.TestSuite');

/**
 * FileManager Controller Test
 *
 * PHP version 5
 *
 * @category Test
 * @package  Croogo
 * @version  1.4
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class FileManagerControllerTest extends CroogoControllerTestCase {

/**
 * fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.users.aco',
		'plugin.users.aro',
		'plugin.users.aros_aco',
		'plugin.settings.setting',
		'plugin.users.role',
		'plugin.taxonomy.type',
		'plugin.taxonomy.types_vocabulary',
		'plugin.taxonomy.vocabulary',
	);

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->FileManager);
	}

/**
 * testAdminBrowseRestricted
 *
 * @return void
 */
	public function testAdminBrowseRestricted() {
		$url = '/admin/file_manager/file_manager/browse?path=' . urlencode(APP . '../../..');
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		$this->FileManager = new FileManagerController($request, $response);
		$this->FileManager->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => 'file_manager',
			'controller' => 'file_manager',
			'action' => 'admin_browse',
			'named' => array(),
			'pass' => array(),
			'?' => array(
				'path' => APP . '../../..',
			),
		));
		$this->FileManager->constructClasses();
		$this->FileManager->Components->unload('Croogo.Croogo');
		$this->FileManager->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->FileManager->startupProcess();
		$this->FileManager->invokeAction($this->FileManager->request);
		$message = $this->FileManager->Session->read('Message.flash.message');
		$this->assertContains('is restricted', $message);
	}

/**
 * testAdminBrowse
 *
 * @return void
 */
	public function testAdminBrowse() {
		$url = '/admin/file_manager/file_manager/browse?path=' . urlencode(APP);
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		$this->FileManager = new FileManagerController($request, $response);
		$this->FileManager->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => 'file_manager',
			'controller' => 'file_manager',
			'action' => 'admin_browse',
			'named' => array(),
			'pass' => array(),
			'?' => array(
				'path' => APP,
			),
		));
		$this->FileManager->constructClasses();
		$this->FileManager->Components->unload('Croogo.Croogo');
		$this->FileManager->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->FileManager->startupProcess();
		$this->FileManager->invokeAction($this->FileManager->request);
		$message = $this->FileManager->Session->read('Message.flash.message');
		$this->assertEmpty($message);
	}

/**
 * testAdminBrowseSubfolder
 *
 * @return void
 */
	public function testAdminBrowseSubfolder() {
		$url = '/admin/file_manager/file_manager/browse?path=' . urlencode(APP) . 'webroot';
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		$this->FileManager = new FileManagerController($request, $response);
		$this->FileManager->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => 'file_manager',
			'controller' => 'file_manager',
			'action' => 'admin_browse',
			'named' => array(),
			'pass' => array(),
			'?' => array(
				'path' => APP . 'webroot',
			),
		));
		$this->FileManager->constructClasses();
		$this->FileManager->Components->unload('Croogo.Croogo');
		$this->FileManager->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
		));
		$this->FileManager->startupProcess();
		$this->FileManager->invokeAction($this->FileManager->request);
		$message = $this->FileManager->Session->read('Message.flash.message');
		$this->assertEmpty($message);
	}

}
