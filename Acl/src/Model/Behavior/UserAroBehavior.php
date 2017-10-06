<?php

namespace Croogo\Acl\Model\Behavior;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * UserAro Behavior
 *
 * @category Behavior
 * @package  Croogo.Acl.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class UserAroBehavior extends Behavior
{

/**
 * Setup
 */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->_setupMultirole($this->_table);
    }

/**
 * Enable Multiple Role, dynamically bind User Habtm Role
 */
    protected function _setupMultirole(Table $model)
    {
        if (!Configure::read('Access Control.multiRole')) {
            return;
        }
        $model->belongsToMany('Roles', [
            'className' => 'Croogo/Users.Roles',
            'saveStrategy' => 'replace',
        ]);
    }

    /**
     * afterSave
     *
     * @param Model $model
     * @param bool $created
     * @return void
     */
    public function afterSave(Event $event, Entity $entity)
    {
        // update ACO alias
        if (!empty($entity->username)) {
            $model = $event->subject();
            $arosTable = TableRegistry::get('Aros');

            $ref = ['model' => $model->alias(), 'foreign_key' => $entity->id];
            $node = $model->node($ref);
            $aro = $node->firstOrFail();

            $aro->alias = $entity->username;

            $arosTable->save($aro);
        }
        Cache::clearGroup('acl', 'permissions');
    }

    /**
     * afterDelete
     */
    public function afterDelete(Event $event)
    {
        Cache::clearGroup('acl', 'permissions');
    }

}
