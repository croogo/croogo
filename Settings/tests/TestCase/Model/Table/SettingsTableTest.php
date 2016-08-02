<?php
namespace Croogo\Settings\Test\TestCase\Model;

use Cake\ORM\TableRegistry;
use Croogo\Core\TestSuite\TestCase;

/**
 * @property \Croogo\Settings\Model\Table\SettingsTable Settings
 */
class SettingsTableTest extends TestCase
{
    public $fixtures = [
        'plugin.croogo/core.settings',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Settings = TableRegistry::get('Croogo/Settings.Settings');
    }

    public function testWriteNew()
    {
        $this->Settings->write('Prefix.key', 'value');
        $prefixAnything = $this->Settings->findByKey('Prefix.key')->first();
        $this->assertEquals('value', $prefixAnything->value);
    }

    public function testWriteUpdate()
    {
        $this->Settings->write('Site.title', 'My new site title', ['editable' => 1]);
        $siteTitle = $this->Settings->findByKey('Site.title')->first();
        $this->assertEquals('My new site title', $siteTitle->value);

        $this->Settings->write('Site.title', 'My new site title', ['input_type' => 'checkbox']);
        $siteTitle = $this->Settings->findByKey('Site.title')->first();
        $this->assertTrue($siteTitle->editable);

        $this->Settings->write('Site.title', 'My new site title', ['input_type' => 'textarea', 'editable' => false]);
        $siteTitle = $this->Settings->findByKey('Site.title')->first();
        $this->assertEquals('textarea', $siteTitle->input_type);
        $this->assertFalse($siteTitle->editable);
    }

    public function testDeleteKey()
    {
        $this->Settings->write('Prefix.key', 'value');
        $this->Settings->deleteKey('Prefix.key');
        $hasAny = $this->Settings->exists([
            'Settings.key' => 'Prefix.key',
        ]);
        $this->assertFalse($hasAny);
    }
}
