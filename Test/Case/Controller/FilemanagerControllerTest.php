<?php
App::uses('FilemanagerController', 'Controller');
App::uses('CroogoTestCase', 'TestSuite');

class TestFilemanagerController extends FilemanagerController {

	protected function _stop($status = 0) {
		$this->stopped = $status;
	}
}

class FilemanagerControllerTest extends CroogoTestCase {

	public $fixtures = array(
		'aco',
		'aro',
		'aros_aco',
	);

	public $Filemanaer = null;

	public function tearDown() {
		if (isset($this->Filemanager)) {
			$this->Filemanager->Session->destroy();
			unset($this->Filemanager);
		}
		ClassRegistry::flush();
	}

	public function testAdminBrowseRestricted() {
		$url = '/admin/filemanager/browse?path=' . urlencode(APP . '../../..');
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		$this->Filemanager = new TestFilemanagerController($request, $response);
		$this->Filemanager->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => false,
			'controller' => 'file_manager',
			'action' => 'admin_browse',
			'named' => array(),
			'pass' => array(),
			'?' => array(
				'path' => APP . '../../..',
				),
			));
		$this->Filemanager->constructClasses();
		$this->Filemanager->Components->unload('Croogo');
		$this->Filemanager->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
			));
		$this->Filemanager->startupProcess();
		$this->Filemanager->invokeAction($this->Filemanager->request);
		$message = $this->Filemanager->Session->read('Message.flash.message');
		$this->assertContains('is restricted', $message);
	}

	public function testAdminBrowse() {
		$url = '/admin/filemanager/browse?path=' . urlencode(APP);
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		$this->Filemanager = new TestFilemanagerController($request, $response);
		$this->Filemanager->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => false,
			'controller' => 'file_manager',
			'action' => 'admin_browse',
			'named' => array(),
			'pass' => array(),
			'?' => array(
				'path' => APP,
				),
			));
		$this->Filemanager->constructClasses();
		$this->Filemanager->Components->unload('Croogo');
		$this->Filemanager->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
			));
		$this->Filemanager->startupProcess();
		$this->Filemanager->invokeAction($this->Filemanager->request);
		$message = $this->Filemanager->Session->read('Message.flash.message');
		$this->assertEmpty($message);
	}

	public function testAdminBrowseSubfolder() {
		$url = '/admin/filemanager/browse?path=' . urlencode(APP) . 'webroot';
		$request = new CakeRequest($url);
		$response = new CakeResponse();
		$this->Filemanager = new TestFilemanagerController($request, $response);
		$this->Filemanager->request->addParams(array(
			'prefix' => 'admin',
			'admin' => true,
			'plugin' => false,
			'controller' => 'file_manager',
			'action' => 'admin_browse',
			'named' => array(),
			'pass' => array(),
			'?' => array(
				'path' => APP . 'webroot',
				),
			));
		$this->Filemanager->constructClasses();
		$this->Filemanager->Components->unload('Croogo');
		$this->Filemanager->Session->write('Auth.User', array(
			'id' => 1,
			'role_id' => 1,
			'username' => 'admin',
			));
		$this->Filemanager->startupProcess();
		$this->Filemanager->invokeAction($this->Filemanager->request);
		$message = $this->Filemanager->Session->read('Message.flash.message');
		$this->assertEmpty($message);
	}

}
