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
    public function initialize(array $config): void
    {
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
        // UsersTable deklaruje Roles (through RolesUsers) sam; Cake 5 rzuca
        // wyjatek przy ponownej deklaracji aliasu asocjacji
        if ($model->associations()->has('Roles')) {
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
     * @param Model $event
     * @param bool $entity
     * @return void
     */
    public function afterSave(\Cake\Event\EventInterface $event, Entity $entity): void
    {
        // update ACO alias
        if (!empty($entity->username)) {
            $model = $event->getSubject();
            $arosTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Aros');

            $ref = ['model' => $model->getAlias(), 'foreign_key' => $entity->id];
            // Cake 5.3: metody behaviora na instancji tabeli deprecated
            $node = $model->getBehavior('Acl')->node($ref);
            $aro = $node->firstOrFail();

            $aro->alias = $entity->username;

            $arosTable->save($aro);
        }
        Cache::clearGroup('acl', 'permissions');
    }

    /**
     * afterDelete
     */
    public function afterDelete(\Cake\Event\EventInterface $event): void
    {
        Cache::clearGroup('acl', 'permissions');
    }
}
