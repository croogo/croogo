<?php

namespace Croogo\Users\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

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
