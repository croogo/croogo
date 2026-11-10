<?php

namespace Croogo\Users\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

class RolesTable extends CroogoTable
{

    /**
     * Display fields for this model
     *
     * @var array
     */
    protected $_displayFields = [
        'title',
        'alias',
    ];

    public function initialize(array $config): void
    {
        // alias 'Acl' (przy className alias = pelna nazwa, wiec bez prefiksu pluginu)
        $this->addBehavior('Acl', [
            'className' => 'Croogo/Core.CroogoAcl',
            'type' => 'requester'
        ]);
        $this->addBehavior('Search.Search');
        $this->addBehavior('Croogo/Core.Trackable');
        $this->addBehavior('Croogo/Core.Aliasable');
    }
}
