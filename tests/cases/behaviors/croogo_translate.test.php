<?php
App::import('Model', 'Node');

class CroogoTranslateBehaviorTestCase extends CakeTestCase {

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

    public function startTest() {
        $this->Node =& ClassRegistry::init('Node');
        $this->Node->Behaviors->attach('CroogoTranslate', array(
            'title' => 'titleTranslation',
        ));
    }

    public function testSaveTranslation() {
        $this->Node->id = 20; // About
        $this->Node->locale = 'ben';
        $this->Node->saveTranslation(array(
            'Node' => array(
                'title' => 'About [Translated in Bengali]',
            ),
        ));
        $about = $this->Node->findById('20');
        $this->assertEqual($about['Node']['title'], 'About [Translated in Bengali]');
    }

    public function endTest() {
        unset($this->Node);
        ClassRegistry::flush();
    }
}
?>