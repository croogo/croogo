<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Example Helper
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
class ExampleHelper extends AppHelper {

/**
 * Other helpers used by this helper
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'Html',
		'Croogo.Layout',
		'Nodes' => array('className' => 'Nodes.Nodes'),
	);

/**
 * Before render callback. Called before the view file is rendered.
 *
 * @return void
 */
	public function beforeRender($viewFile) {
	}

/**
 * After render callback. Called after the view file is rendered
 * but before the layout has been rendered.
 *
 * @return void
 */
	public function afterRender($viewFile) {
	}

/**
 * Before layout callback. Called before the layout is rendered.
 *
 * @return void
 */
	public function beforeLayout($layoutFile) {
	}

/**
 * After layout callback. Called after the layout has rendered.
 *
 * @return void
 */
	public function afterLayout($layoutFile) {
	}

/**
 * Called after LayoutHelper::setNode()
 *
 * @return void
 */
	public function afterSetNode() {
		// field values can be changed from hooks
		$this->Nodes->field('title', $this->Nodes->field('title') . ' [Modified by ExampleHelper]');
	}

/**
 * Called before LayoutHelper::nodeInfo()
 *
 * @return string
 */
	public function beforeNodeInfo() {
		return '<p>beforeNodeInfo</p>';
	}

/**
 * Called after LayoutHelper::nodeInfo()
 *
 * @return string
 */
	public function afterNodeInfo() {
		return '<p>afterNodeInfo</p>';
	}

/**
 * Called before LayoutHelper::nodeBody()
 *
 * @return string
 */
	public function beforeNodeBody() {
		return '<p>beforeNodeBody</p>';
	}

/**
 * Called after LayoutHelper::nodeBody()
 *
 * @return string
 */
	public function afterNodeBody() {
		return '<p>afterNodeBody</p>';
	}

/**
 * Called before LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
	public function beforeNodeMoreInfo() {
		return '<p>beforeNodeMoreInfo</p>';
	}

/**
 * Called after LayoutHelper::nodeMoreInfo()
 *
 * @return string
 */
	public function afterNodeMoreInfo() {
		return '<p>afterNodeMoreInfo</p>';
	}
}
