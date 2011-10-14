<?php
class BehaviorsGroupTest extends TestSuite {
/**
 * label property
 *
 * @var string
 * @access public
 */
    var $label = 'All behavior tests';

    function BehaviorsGroupTest() {
        TestManager::addTestCasesFromDirectory($this, APP_TEST_CASES.DS.'behaviors');
    }
}
?>