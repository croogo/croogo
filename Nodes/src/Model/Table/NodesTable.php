<?php

namespace Croogo\Nodes\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

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

}
