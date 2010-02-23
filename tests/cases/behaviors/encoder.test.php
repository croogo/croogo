<?php
App::import('Model', 'Node');
class EncoderBehaviorTestCase extends CakeTestCase {

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

    function testEncodeWithoutKeys() {
        $array = array('hello', 'world');
        $encoded = $this->Node->encodeData($array);
        $this->assertEqual($encoded, '["hello","world"]');
    }

    function testEncodeWithKeys() {
        $array = array(
            'first' => 'hello',
            'second' => 'world',
        );
        $encoded = $this->Node->encodeData($array, array(
            'json' => true,
            'trim' => false,
        ));
        $this->assertEqual($encoded, '{"first":"hello","second":"world"}');
    }

    function testDecodeWithoutKeys() {
        $encoded = '["hello","world"]';
        $array = $this->Node->decodeData($encoded);
        $this->assertEqual($array, array('hello', 'world'));
    }

    function testDecodeWithKeys() {
        $encoded = '{"first":"hello","second":"world"}';
        $array = $this->Node->decodeData($encoded);
        $this->assertEqual($array, array(
            'first' => 'hello',
            'second' => 'world',
        ));
    }

    function endTest() {
        unset($this->Node);
        ClassRegistry::flush();
    }
}
?>