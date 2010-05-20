<?php
App::import('Model', 'Node');

class NodeTestCase extends CakeTestCase {

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

    public function testCacheTerms() {
        $this->Node->data = array(
            'Node' => array(),
            'Taxonomy' => array(
                'Taxonomy' => array(1, 2), // uncategorized, and announcements
            ),
        );
        $this->Node->__cacheTerms();
        $this->assertEqual($this->Node->data['Node']['terms'], '{"1":"uncategorized","2":"announcements"}');
    }

    public function endTest() {
        unset($this->Node);
        ClassRegistry::flush();
    }
}
?>