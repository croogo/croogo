<?php

namespace Croogo\Meta\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

/**
 * Meta
 *
 * @category Meta.Model
 * @package  Croogo.Meta
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaTable extends CroogoTable
{
    public function initialize(array $config)
    {
        $this->table('meta');
        $this->addBehavior('Croogo/Core.Trackable');

        parent::initialize($config);
    }
}
