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
		parent::setUp();
	}

	public function tearDown() {
		unset($this->FileManager);
		parent::tearDown();
	}

/**
 * @group isEditable
 */
	public function testIsEditableShouldReturnTrueWhenPathIsWithinEditablePaths() {
		$isEditable = $this->FileManager->isEditable($this->__testAppPath . 'renameMeTooPlease.txt');
		$this->assertTrue($isEditable);
	}

/**
 * @group isEditable
 */
	public function testIsEditableShouldReturnFalseWhenPathIsOutsideEditablePaths() {
		$isEditable = $this->FileManager->isEditable('/var/log/apache2');
		$this->assertFalse($isEditable);
	}

/**
 * @group isDeletable
 */
	public function testIsDeletable() {
		$isDeletable = $this->FileManager->isDeletable($this->__testAppPath .  'renameMeTooPlease.txt');
		$this->assertTrue($isDeletable);
	}

/**
 * @group isDeletable
 */
	public function testIsDeletableOnRestrictedPath() {
		$isDeletable = $this->FileManager->isDeletable('/usr/bin/php');
		$this->assertFalse($isDeletable);
	}

	public function testGetEditablePaths() {
		$expectedPaths = array('/foo/bar', '/no/pasaran');
		Configure::write('FileManager.editablePaths', $expectedPaths);

		$paths = $this->FileManager->getEditablePaths();
		$this->assertEquals($expectedPaths, $paths);
	}

	public function testGetEditablePathsWithoutConfig() {
		Configure::delete('FileManager.editablePaths');
		$paths = $this->FileManager->getEditablePaths();
		$this->assertEquals($this->FileManager->defaultEditablePaths, $paths);
	}

	public function testGetDeletablePaths() {
		$expectedPaths = array('/foo/bar', '/nope/nope');
		Configure::write('FileManager.deletablePaths', $expectedPaths);

		$paths = $this->FileManager->getDeletablePaths();
		$this->assertEquals($expectedPaths, $paths);
	}

	public function testGetDeletablePathsWithoutConfig() {
		Configure::delete('FileManager.deletablePaths');
		$paths = $this->FileManager->getDeletablePaths();
		$this->assertEquals($this->FileManager->defaultDeletablePaths, $paths);
	}

	public function testGetDefaultBrowsingPath() {
		$expectedPath = '/a/more/secure/path';
		Configure::write('FileManager.defaultBrowsePath', $expectedPath);
		$browsingPath = $this->FileManager->getDefaultBrowsingPath();

		$this->assertEquals($expectedPath, $browsingPath);
	}

	public function testGetDefaultBrowsingPathWithoutConfig() {
		$browsingPath = $this->FileManager->getDefaultBrowsingPath();
		$this->assertEquals(APP. DS .WEBROOT_DIR, $browsingPath);
	}

/**
 * @group rename
 */
	public function testRenameShouldReturnedTrueOnSuccess() {
		$oldPath = $this->__testAppPath . 'renameMe';
		$newPath = $this->__testAppPath . 'renamed';

		$this->assertTrue($this->FileManager->rename($oldPath, $newPath));
		$this->FileManager->rename($newPath, $oldPath);
	}

/**
 * @group rename
 */
	public function testRenameShouldRenamedOldFileToNewFile() {
		$oldPath = $this->__testAppPath . 'renameMeTooPlease.txt';
		$newPath = $this->__testAppPath . 'renamed.txt';

		$this->FileManager->rename($oldPath, $newPath);
		$this->assertTrue(file_exists($newPath) && !file_exists($oldPath));

		$this->FileManager->rename($newPath, $oldPath);
	}

/**
 * @group rename
 */
	public function testRenameShouldRenamedOldFolderToNewFolder() {
		$oldPath = $this->__testAppPath . 'renameMe';
		$newPath = $this->__testAppPath . 'renamed';

		$this->FileManager->rename($oldPath, $newPath);
		$this->assertTrue(is_dir($newPath) && !is_dir($oldPath));

		$this->FileManager->rename($newPath, $oldPath);
	}

/**
 * Convenient methods for testsuite
 */
	private function __setFilePathsForTests() {
		Configure::write('FileManager.editablePaths', array($this->__testAppPath));
		Configure::write('FileManager.deletablePaths', array($this->__testAppPath));
	}

}
