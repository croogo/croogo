<?php

namespace Croogo\Users\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class UsersTable extends CroogoTable {

	protected $_displayFields = array(
		'id',
		'role.title' => 'Role',
		'username',
		'name',
		'status' => array('type' => 'boolean'),
		'email',
	);

	public $filterArgs = array(
		'name' => array('type' => 'like', 'field' => array('Users.name', 'Users.username')),
		'role_id' => array('type' => 'value'),
	);

	public function initialize(array $config) {
		parent::initialize($config);

		$this->belongsTo('Croogo/Users.Roles');
		$this->addBehavior('Search.Searchable');
	}
}
