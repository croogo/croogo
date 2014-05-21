<?php

namespace Croogo\Nodes\Controller\Component;
App::uses('BaseApiComponent', 'Croogo.Controller/Component');

class NodeApiComponent extends BaseApiComponent {

/**
 * API Version
 */
	protected $_apiVersion = 'v1.0';

/**
 * API methods
 */
	protected $_apiMethods = array(
		'lookup',
	);

/**
 * List nodes with filter capability as defined in Node::$filterArgs
 *
 * This will be useful for ajax autocompletion
 */
	public function lookup(Controller $controller) {
		$request = $controller->request;
		$controller->Prg->commonProcess();

		$Node = $controller->{$controller->modelClass};
		$Node->Behaviors->attach('Nodes.NodeApiResultFormatter');
		$controller->paginate = array(
			'fields' => array(
				'id', 'parent_id', 'type', 'user_id', 'title', 'slug',
				'body', 'excerpt', 'status', 'promote', 'path', 'terms',
				'created', 'updated', 'publish_start', 'publish_end',
			),
			'contain' => array(
				'User', 'Meta', 'Taxonomy',
			),
			'conditions' => $Node->parseCriteria($request->query),
		);

		$nodes = $controller->paginate();

		$controller->set('_rootNode', 'nodes');
		$controller->set('node', $nodes);
		$controller->set('_serialize', 'node');
	}

}
