<?php
class ModelsGroupTest extends TestSuite {
/**
 * label property
 *
 * @var string
 * @access public
 */
    var $label = 'All model tests';

    function ModelsGroupTest() {
        TestManager::addTestCasesFromDirectory($this, APP_TEST_CASES.DS.'models');
    }
}
?>