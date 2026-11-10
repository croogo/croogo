<?php
use App\FileManager\Model\FileManager;
use App\Croogo\TestSuite\CroogoTestCase;

class FileManagerTest extends CroogoTestCase
{

    public $FileManager;

    protected array $fixtures = [
        'plugin.Settings'
    ];

    private $__testAppPath;

    public function setUp(): void
    {
        $this->FileManager = new FileManager(false, null, null, null);
        $this->__testAppPath = \Plugin::path('FileManager') . 'Test' . DS . 'test_app' . DS;
        $this->__setFilePathsForTests();
        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->FileManager);
        parent::tearDown();
    }

    /**
     * @group isEditable
     */
    public function testIsEditableShouldReturnTrueWhenPathIsWithinEditablePaths(): void
    {
        $isEditable = $this->FileManager->isEditable($this->__testAppPath . DS . 'renameMeTooPlease.txt');
        $this->assertTrue($isEditable);
    }

    /**
     * @group isEditable
     */
    public function testIsEditableShouldReturnFalseWhenPathIsOutsideEditablePaths(): void
    {
        $isEditable = $this->FileManager->isEditable('/var/log/apache2');
        $this->assertFalse($isEditable);
    }

    /**
     * @group rename
     */
    public function testRenameShouldReturnedTrueOnSuccess(): void
    {
        $oldPath = $this->__testAppPath . DS . 'renameMe';
        $newPath = $this->__testAppPath . DS . 'renamed';

        $this->assertTrue($this->FileManager->rename($oldPath, $newPath));
        $this->FileManager->rename($newPath, $oldPath);
    }

    /**
     * @group rename
     */
    public function testRenameShouldRenamedOldFileToNewFile(): void
    {
        $oldPath = $this->__testAppPath . DS . 'renameMeTooPlease.txt';
        $newPath = $this->__testAppPath . DS . 'renamed.txt';

        $this->FileManager->rename($oldPath, $newPath);
        $this->assertTrue(file_exists($newPath) && !file_exists($oldPath));

        $this->FileManager->rename($newPath, $oldPath);
    }

    /**
     * @group rename
     */
    public function testRenameShouldRenamedOldFolderToNewFolder(): void
    {
        $oldPath = $this->__testAppPath . 'renameMe';
        $newPath = $this->__testAppPath . 'renamed';

        $this->FileManager->rename($oldPath, $newPath);
        $this->assertTrue(is_dir($newPath) && !is_dir($oldPath));

        $this->FileManager->rename($newPath, $oldPath);
    }

    /**
     * Convenient methods for testsuite
     */
    private function __setFilePathsForTests(): void
    {
        Configure::write('FileManager.editablePaths', [$this->__testAppPath]);
        Configure::write('FileManager.deletablePaths', [$this->__testAppPath]);
    }
}
