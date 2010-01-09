<?php
App::import('Model', 'Node');

class NodeTestCase extends CakeTestCase {

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

    function testCacheTerms() {
        $this->Node->data = array(
            'Node' => array(),
            'Term' => array(
                'Term' => array(1, 2), // uncategorized, and announcements
            ),
        );
        $this->Node->__cache_terms();
        $this->assertEqual($this->Node->data['Node']['terms'], '{"1":"uncategorized","2":"announcements"}');
    }

    function endTest() {
        unset($this->Node);
        ClassRegistry::flush();
    }
}
?>