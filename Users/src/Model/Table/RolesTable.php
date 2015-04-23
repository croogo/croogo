<?php

namespace Croogo\Users\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class RolesTable extends CroogoTable {

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
}
