<?php

/**
 * Nodes Component
 *
 * PHP version 5
 *
 * @category Component
 * @package  Croogo.Nodes.Controller.Component
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
 * initialize
 *
 * @param Controller $controller instance of controller
 */
	public function initialize(Controller $controller) {
		$this->controller = $controller;
		if (isset($controller->Node)) {
			$this->Node = $controller->Node;
		} else {
			$this->Node = ClassRegistry::init('Nodes.Node');
		}

		if (Configure::read('Access Control.multiRole')) {
			Configure::write('Acl.classname', 'Acl.HabtmDbAcl');
			App::uses('HabtmDbAcl', 'Acl.Controller/Component/Acl');
			$controller->Acl->adapter('HabtmDbAcl');
			$this->Node->User->bindModel(array(
				'hasAndBelongsToMany' => array(
					'Role' => array(
						'className' => 'Users.Role',
						'with' => 'Users.RolesUser',
					),
				),
			), false);
		}
	}

/**
 * Startup
 *
 * @param Controller $controller instance of controller
 * @return void
 */
	public function startup(Controller $controller) {
		if (!isset($controller->request->params['admin']) && !isset($controller->request->params['requested'])) {
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
			$options = Hash::merge($_nodeOptions, $options);
			$options['limit'] = str_replace('"', '', $options['limit']);
			$node = $this->Node->find($options['find'], array(
				'conditions' => $options['conditions'],
				'order' => $options['order'],
				'limit' => $options['limit'],
				'cache' => array(
					'prefix' => 'nodes_' . $alias,
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
		$controller->set('nodes_for_layout', $this->nodesForLayout);
	}

}
