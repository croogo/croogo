<?php

App::uses('Component', 'Controller');

/**
 * Nodes Component
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

		$this->_hookLinkChoosers($controller);
	}

/**
 * Nodes
 *
 * Nodes will be available in this variable in views: $nodes_for_layout
 *
 * @return void
 */
	public function nodes() {
		$roleId = $this->controller->Croogo->roleId();

		$nodes = $this->controller->Blocks->blocksData['nodes'];
		$_nodeOptions = array(
			'find' => 'all',
			'conditions' => array(
				'Node.status' => 1,
				'OR' => array(
					'Node.visibility_roles' => '',
					'Node.visibility_roles LIKE' => '%"' . $roleId . '"%',
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
 * hookLinkChoosers
 *
 * Adds link chooosers to the add link page
 *
 * @return void
 */

	protected function _hookLinkChoosers(Controller $controller){

			$type = ClassRegistry::init('Taxonomy.Type');
			$types = $type->find('all',array('fields'=>array('alias','title','description')));

			$linkChoosers = array();
			foreach($types as $type){
				$linkChoosers[$type['Type']['title']] = array(
					'title' => $type['Type']['title'],
					'description'=>$type['Type']['description'],
					'url'=>array(
						'plugin'=>'nodes',
						'controller'=>'nodes',
						'action'=>'index',
						'?'=>array(
							'type'=>$type['Type']['alias'],
							'chooser' => 1,
							'KeepThis' => true,
							'TB_iframe' => true,
							'height' => 400,
							'width' => 600
							)
						)
					);
			}
			Croogo::mergeConfig('Menus.linkChoosers',$linkChoosers);
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
