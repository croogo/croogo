<?php
App::import('Model', 'Node');
class UrlBehaviorTestCase extends CakeTestCase {

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
        $this->Node =& ClassRegistry::init('Node');
    }

    public function testSingle() {
        $helloWorld = $this->Node->findBySlug('hello-world');
        $this->assertEqual($helloWorld['Node']['url'], array(
            'plugin' => false,
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
            'slug' => 'hello-world',
        ));
    }

    public function testMultiple() {
        $result = $this->Node->find('all');
        $this->assertEqual($result['0']['Node']['url'], array(
            'plugin' => false,
            'controller' => 'nodes',
            'action' => 'view',
            'type' => $result['0']['Node']['type'],
            'slug' => $result['0']['Node']['slug'],
        ));
    }

    public function endTest() {
        unset($this->Node);
        ClassRegistry::flush();
    }
}
?>