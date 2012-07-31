<?php

/**
 * Nodes Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Nodes
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class NodesComponent extends Component {

/**
 * Nodes for layout
 *
 * @var string
 * @access public
 */
	public $nodesForLayout = array();

/**
 * Startup
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		$this->controller = $controller;
		$controller->loadModel('Nodes.Node');

		if (!isset($this->controller->request->params['admin']) && !isset($this->controller->request->params['requested'])) {
			$this->nodes();
		}
	}

/**
 * Nodes
 *
 * Nodes will be available in this variable in views: $nodes_for_layout
 *
 * @return void
 */
	public function nodes() {
		$nodes = $this->controller->Blocks->blocksData['nodes'];
		$_nodeOptions = array(
			'find' => 'all',
			'conditions' => array(
				'Node.status' => 1,
				'OR' => array(
					'Node.visibility_roles' => '',
					'Node.visibility_roles LIKE' => '%"' . $this->roleId . '"%',
				),
			),
			'order' => 'Node.created DESC',
			'limit' => 5,
		);

		foreach ($nodes as $alias => $options) {
			$options = Set::merge($_nodeOptions, $options);
			$options['limit'] = str_replace('"', '', $options['limit']);
			$node = $this->controller->Node->find($options['find'], array(
				'conditions' => $options['conditions'],
				'order' => $options['order'],
				'limit' => $options['limit'],
				'cache' => array(
					'prefix' => 'croogo_nodes_' . $alias . '_',
					'config' => 'croogo_nodes',
				),
			));
			$this->nodesForLayout[$alias] = $node;
		}
	}

/**
 * beforeRender
 *
 * @param object $controller instance of controller
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$this->controller = $controller;
		$this->controller->set('nodes_for_layout', $this->nodesForLayout);
	}

}
