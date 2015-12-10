<?php

namespace Croogo\Core\Test\TestCase\Configure;

use Cake\Core\Plugin;
use Croogo\Core\Configure\CroogoJsonReader;
use Croogo\Core\TestSuite\CroogoTestCase;

class MockCroogoJsonReader extends CroogoJsonReader
{

    public $written = null;

    public function getPath()
    {
        return $this->_path;
    }
}

class CroogoJsonReaderTest extends CroogoTestCase
{

    /**
     * @var CroogoJsonReader
     */
    private $CroogoJsonReader;

    /**
     * @var string
     */
    private $testFile;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->CroogoJsonReader = $this->getMock(
            '\\Croogo\\Core\\Test\\TestCase\\Configure\\MockCroogoJsonReader',
            null,
            [Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS . 'config' . DS]
        );
        $this->testFile = $this->CroogoJsonReader->getPath() . 'test.json';
    }

/**
 * tearDown
 */
    public function tearDown()
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

/**
 * testDefaultPath
 */
    public function testDefaultPath()
    {
        $path = $this->CroogoJsonReader->getPath();
        $this->assertEquals(Plugin::path('Croogo/Core') . 'tests' . DS . 'test_app' . DS . 'config' . DS, $path);
    }

/**
 * testRead
 */
    public function testRead()
    {
        $settings = $this->CroogoJsonReader->read('settings', 'settings');
        $expected = [
            'acl_plugin' => 'Acl',
            'email' => 'you@your-site.com',
            'feed_url' => '',
            'locale' => 'eng',
            'status' => 1,
            'tagline' => 'A CakePHP powered Content Management System.',
            'theme' => '',
            'timezone' => 0,
            'title' => 'Croogo - Test',
        ];
        $this->assertEquals($expected, $settings['Site']);
    }

/**
 * testDump
 */
    public function testDump()
    {
        $settings = [
            'Site' => [
                'title' => 'Croogo - Test (Edited)',
            ],
            'Reading' => [
                'date_time_format' => 'Y m d',
                'nodes_per_page' => 20,
            ],
            'Nested' => [
                'StringValue' => 'Is Fine',
                'AnotherArray' => [
                    'should' => 'be',
                    'persisted' => 'correctly',
                ],
            ],
            'Hook' => [
                'someKey' => 'value',
                'model_properties' => ['ignored', 'to', 'oblivion'],
                'controller_properties' => ['ignored', 'to', 'oblivion'],
            ],
        ];
        $this->CroogoJsonReader->dump(basename($this->testFile), $settings);
        $expected = <<<END
{
\s+"Site": {
\s+"title": "Croogo - Test \(Edited\)"
\s+},
\s+"Reading": {
\s+"date_time_format": "Y m d",
\s+"nodes_per_page": 20
\s+},
\s+"Nested": {
\s+"StringValue": "Is Fine",
\s+"AnotherArray": {
\s+"should": "be",
\s+"persisted": "correctly"
\s+}
\s+},
\s+"Hook": {
\s+"someKey": "value"
\s+}
}
END;
        $this->assertRegExp($expected, file_get_contents($this->testFile));
    }
}
