<?php

namespace Croogo\Users\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class UsersTable extends CroogoTable {

	public $filterArgs = array(
		'name' => array('type' => 'like', 'field' => array('User.name', 'User.username')),
		'role_id' => array('type' => 'value'),
	);

	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('Croogo/Users.Roles');
		$this->addBehavior('Search.Searchable');
	}
}
