<?php

namespace Croogo\Nodes\Model\Table;

use Croogo\Croogo\Croogo;
use Croogo\Croogo\Model\Table\CroogoTable;
use Croogo\Nodes\Model\Entity\Node;

class NodesTable extends CroogoTable {

	public $filterArgs = array(
		'q' => array('type' => 'query', 'method' => 'filterPublishedNodes'),
		'filter' => array('type' => 'query', 'method' => 'filterNodes'),
		'title' => array('type' => 'like'),
		'type' => array('type' => 'value'),
		'status' => array('type' => 'value'),
		'promote' => array('type' => 'value'),
	);


	public function initialize(array $config) {
		parent::initialize($config);

		$this->addBehavior('Croogo/Croogo.Encoder');
		$this->addBehavior('Search.Searchable');
		$this->belongsTo('Croogo/Users.Users');
	}

/**
 * Create/update a Node record
 *
 * @param $node array Node data
 * @param $typeAlias string Node type alias
 * @return mixed see Model::saveAll()
 */
	public function saveNode(Node $node, $typeAlias = self::DEFAULT_TYPE) {
		$result = false;

//		$node = $this->formatNode($node, $typeAlias);
		$event = Croogo::dispatchEvent('Model.Node.beforeSaveNode', $this, compact('node', 'typeAlias'));
		$result = $this->save($event->data['node']);
		Croogo::dispatchEvent('Model.Node.afterSaveNode', $this, $event->data);

		return $result;
	}

/**
 * Format data for saving
 *
 * @param array $data Node and related data, eg Taxonomy and Role
 * @param string $typeAlias string Node type alias
 * @return array formatted data
 * @throws InvalidArgumentException
 */
	public function formatNode($data, $typeAlias = self::DEFAULT_TYPE) {
		$roles = $type = array();

		if (!array_key_exists($this->alias, $data)) {
			$data = array($this->alias => $data);
		} else {
			$data = $data;
		}

		if (empty($data[$this->alias]['path'])) {
			$data[$this->alias]['path'] = $this->_getNodeRelativePath($data);
		}

		if (!array_key_exists('Role', $data) || empty($data['Role']['Role'])) {
			$roles = '';
		} else {
			$roles = $data['Role']['Role'];
		}

		$data[$this->alias]['visibility_roles'] = $this->encodeData($roles);

		return $data;
	}

}
