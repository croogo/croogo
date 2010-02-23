<?php
App::import('Model', 'Setting');

class SettingTestCase extends CakeTestCase {

    var $fixtures = array(
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
        'nodes_term',
        'region',
        'role',
        'setting',
        'term',
        'type',
        'types_vocabulary',
        'user',
        'vocabulary',
    );

    function startTest() {
         $this->Setting =& ClassRegistry::init('Setting');
    }

    function testWriteNew() {
        $this->Setting->write('Prefix.key', 'value');
        $prefixAnything = $this->Setting->findByKey('Prefix.key');
        $this->assertEqual('value', $prefixAnything['Setting']['value']);
    }

    function testWriteUpdate() {
        $this->Setting->write('Site.title', 'My new site title');
        $siteTitle = $this->Setting->findByKey('Site.title');
        $this->assertEqual('My new site title', $siteTitle['Setting']['value']);
    }

    function testDeleteKey() {
        $this->Setting->write('Prefix.key', 'value');
        $this->Setting->deleteKey('Prefix.key');
        $hasAny = $this->Setting->hasAny(array(
            'Setting.key' => 'Prefix.key',
        ));
        $this->assertFalse($hasAny);
    }

    function testWriteConfiguration() {
        $this->Setting->writeConfiguration();
        $this->assertEqual(Configure::read('Site.title'), 'Croogo');
    }

    function endTest() {
        unset($this->Setting);
        ClassRegistry::flush();
    }
}
?>