<?php

namespace Croogo\Acl\Model\Table;

use Cake\Utility\Hash;

/**
 * AclAro Model
 *
 * @category Model
 * @package  Croogo.Acl.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class ArosTable extends \Acl\Model\Table\ArosTable
{

/**
 * Get a list of Role AROs
 *
 * @return array array of Aro.id indexed by Role.id
 */
    public function getRoles($roles)
    {
        $aros = $this->find('all', [
            'conditions' => [
                'Aros.model' => 'Roles',
                'Aros.foreign_key IN' => array_keys($roles->toArray()),
            ],
        ]);
        return collection($aros)->combine('foreign_key', 'id')->toArray();
    }
}
