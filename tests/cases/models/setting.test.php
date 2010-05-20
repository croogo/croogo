<?php
App::import('Model', 'Setting');

class SettingTestCase extends CakeTestCase {

    public $fixtures = array(
        'aco',
        'aro',
        'aros_aco',
        'block',
        'comment',
        'contact',
        'i18n',
        'language',
        'link',
        'menu',
        'message',
        'meta',
        'node',
        'nodes_taxonomy',
        'region',
        'role',
        'setting',
        'taxonomy',
        'term',
        'type',
        'types_vocabulary',
        'user',
        'vocabulary',
    );

    public function startTest() {
         $this->Setting =& ClassRegistry::init('Setting');
    }

    public function testWriteNew() {
        $this->Setting->write('Prefix.key', 'value');
        $prefixAnything = $this->Setting->findByKey('Prefix.key');
        $this->assertEqual('value', $prefixAnything['Setting']['value']);
    }

    public function testWriteUpdate() {
        $this->Setting->write('Site.title', 'My new site title');
        $siteTitle = $this->Setting->findByKey('Site.title');
        $this->assertEqual('My new site title', $siteTitle['Setting']['value']);
    }

    public function testDeleteKey() {
        $this->Setting->write('Prefix.key', 'value');
        $this->Setting->deleteKey('Prefix.key');
        $hasAny = $this->Setting->hasAny(array(
            'Setting.key' => 'Prefix.key',
        ));
        $this->assertFalse($hasAny);
    }

    public function testWriteConfiguration() {
        $this->Setting->writeConfiguration();
        $this->assertEqual(Configure::read('Site.title'), 'Croogo');
    }

    public function endTest() {
        unset($this->Setting);
        ClassRegistry::flush();
    }
}
?>