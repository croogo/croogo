<?php

namespace Croogo\Users\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

class RolesTable extends CroogoTable {

	const ROLE_REGISTERED = 2;

	/**
 * Display fields for this model
 *
 * @var array
 */
    protected $_displayFields = array(
        'id',
        'title',
        'alias',
    );

	public function initialize(array $config)
	{
		parent::initialize($config);

		$this->addBehavior('Acl.Acl', [
			'className' => 'Croogo/Core.CroogoAcl',
			'type' => 'requester'
		]);
	}
}
