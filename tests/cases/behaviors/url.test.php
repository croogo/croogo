<?php
App::import('Model', 'Node');
class UrlBehaviorTestCase extends CakeTestCase {

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
        $this->Node =& ClassRegistry::init('Node');
    }

    function testSingle() {
        $helloWorld = $this->Node->findBySlug('hello-world');
        $this->assertEqual($helloWorld['Node']['url'], array(
            'controller' => 'nodes',
            'action' => 'view',
            'type' => 'blog',
            'slug' => 'hello-world',
        ));
    }

    function testMultiple() {
        $result = $this->Node->find('all');
        $this->assertEqual($result['0']['Node']['url'], array(
            'controller' => 'nodes',
            'action' => 'view',
            'type' => $result['0']['Node']['type'],
            'slug' => $result['0']['Node']['slug'],
        ));
    }

    function endTest() {
        unset($this->Node);
        ClassRegistry::flush();
    }
}
?>