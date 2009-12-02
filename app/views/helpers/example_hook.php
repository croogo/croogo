<?php
/**
 * ExampleHook Helper
 *
 * An example hook helper for demonstrating hook system.
 *
 * @category Helper
 * @package  Croogo
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ExampleHookHelper extends AppHelper {
/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
    var $helpers = array('Html', 'Layout');

    function afterSetNode() {
        // field values can be changed from hooks
        $this->Layout->setNodeField('title', $this->Layout->node('title') . ' [Modified by ExampleHook]');
    }

    function beforeNodeInfo() {
        return '<p>beforeNodeInfo</p>';
    }

    function afterNodeInfo() {
        return '<p>afterNodeInfo</p>';
    }

    function beforeNodeBody() {
        return '<p>beforeNodeBody</p>';
    }

    function afterNodeBody() {
        return '<p>afterNodeBody</p>';
    }

    function beforeNodeMoreInfo() {
        return '<p>beforeNodeMoreInfo</p>';
    }

    function afterNodeMoreInfo() {
        return '<p>afterNodeMoreInfo</p>';
    }
}
?>