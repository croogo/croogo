<?php
App::uses('FileManager', 'FileManager.Model');
App::uses('CroogoTestCase', 'Croogo.TestSuite');

class FileManagerTest extends CroogoTestCase {

	public $FileManager;

	public $fixtures = array(
		'plugin.settings.setting'
	);

	private $__testAppPath;

	public function setUp() {
		$this->FileManager = new FileManager(false, null, null, null);
		$this->__testAppPath = CakePlugin::path('FileManager') . 'Test' . DS . 'test_app' . DS;
		$this->__setFilePathsForTests();
		$this->__setupTestFileSystemStructure();
		parent::setUp();
	}

	public function tearDown() {
		unset($this->FileManager);
		$this->__tearDownTestFileSystemStructure();
		parent::tearDown();
	}

	public function testIsEditableShouldReturnTrueWhenPathIsWithinEditablePaths() {
		$isEditable = $this->FileManager->isEditable($this->__testAppPath . DS . 'renameMeTooPlease.txt');
		$this->assertTrue($isEditable);
	}

	public function testIsEditableShouldReturnFalseWhenPathIsOutsideEditablePaths() {
		$isEditable = $this->FileManager->isEditable('/var/log/apache2');
		$this->assertFalse($isEditable);
	}


	/**
	 * Convenient methods for testsuite
	 */
	private function __setFilePathsForTests() {
		Configure::write('FileManager.editablePaths', array($this->__testAppPath));
		Configure::write('FileManager.deletablePaths', array($this->__testAppPath));
	}

	private function __setupTestFileSystemStructure() {
		mkdir($this->__testAppPath . DS . 'deleteMe');
		mkdir($this->__testAppPath . DS . 'renameMe');
		touch($this->__testAppPath . DS . 'deleteMe' . DS . 'toTestRecursiveDeletion.txt');
		touch($this->__testAppPath . DS . 'renameMe' . DS . 'toBeRenamedSubFile.txt');
		touch($this->__testAppPath . DS . 'deleteMeTooPlease.txt');
		touch($this->__testAppPath . DS . 'renameMeTooPlease.txt');

	}

	private function __tearDownTestFileSystemStructure() {
		@unlink($this->__testAppPath . DS . 'deleteMe' . DS . 'toTestRecursiveDeletion.txt');
		@unlink($this->__testAppPath . DS . 'renameMe' . DS . 'toBeRenamedSubFile.txt');
		@unlink($this->__testAppPath . DS . 'deleteMeTooPlease.txt');
		@unlink($this->__testAppPath . DS . 'renameMeTooPlease.txt');
		rmdir($this->__testAppPath . DS . 'deleteMe');
		rmdir($this->__testAppPath . DS . 'renameMe');
	}

}
