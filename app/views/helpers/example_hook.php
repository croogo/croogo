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
    var $helpers = array(
        'Html',
        'Layout',
    );
/**
 * Called after activating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onActivate(&$controller) {
    }
/**
 * Called after deactivating the hook in ExtensionsHooksController::admin_toggle()
 *
 * @param object $controller Controller
 * @return void
 */
    function onDeactivate(&$controller) {
    }
/**
 * Called after LayoutHelper::setNode()
 *
 * @return void
 */
    function afterSetNode() {
        // field values can be changed from hooks
        $this->Layout->setNodeField('title', $this->Layout->node('title') . ' [Modified by ExampleHook]');
    }
/**
 * Called before LayoutHelper::nodeInfo()
 *
 * @return string
 */
    function beforeNodeInfo() {
        return '<p>beforeNodeInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeInfo()
 *
 * @return string
 */
    function afterNodeInfo() {
        return '<p>afterNodeInfo</p>';
    }
/**
 * Called before LayoutHelper::nodeBody()
 *
 * @return string
 */
    function beforeNodeBody() {
        return '<p>beforeNodeBody</p>';
    }
/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
    function afterNodeBody() {
        return '<p>afterNodeBody</p>';
    }
/**
 * Called before LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    function beforeNodeMoreInfo() {
        return '<p>beforeNodeMoreInfo</p>';
    }
/**
 * Called after LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
    function afterNodeMoreInfo() {
        return '<p>afterNodeMoreInfo</p>';
    }
}
?>