<?php
App::import('Lib', 'CroogoNav');

class CroogoNavTest extends CakeTestCase {
    
    public function testNav() {
        
        // test clear
        CroogoNav::clear();
        $items = CroogoNav::items();
        $this->assertEqual($items, array());
        
        // test first level addition
        $defaults = CroogoNav::getDefaults();
        $extensions = array('title' => 'Extensions');
        CroogoNav::add('extensions', $extensions);
        $result = CroogoNav::items();
        $expected = array('extensions' => Set::merge($defaults, $extensions));
        $this->assertEqual($result, $expected);
        
        // tested nested insertion (1 level)
        $plugins = array('title' => 'Plugins');
        CroogoNav::add('extensions.children.plugins', $plugins);
        $result = CroogoNav::items();
        $expected['extensions']['children']['plugins'] = Set::merge($defaults, $plugins);
        $this->assertEqual($result, $expected);
        
        // 2 levels deep
        $example = array('title' => 'Example');
        CroogoNav::add('extensions.children.plugins.children.example', $example);
        $result = CroogoNav::items();
        
        $expected['extensions']['children']['plugins']['children']['example'] = Set::merge($defaults, $example);
        $this->assertEqual($result, $expected);
    }
    
}
?>