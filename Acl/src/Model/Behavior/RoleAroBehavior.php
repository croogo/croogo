<?php

namespace Croogo\Acl\Model\Behavior;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * RoleAro Behavior
 *
 * @category Behavior
 * @package  Croogo.Acl.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class RoleAroBehavior extends Behavior
{

    protected $_defaultConfig = [
        'implementedFinders' => [
            'roleHierarchy' => 'findRoleHierarchy',
        ],
    ];

/**
 * parentNode
 *
 * @param Model $model
 * @return $mixed
 */
    public function parentNode($model)
    {
        if (!$model->id && empty($model->data)) {
            return null;
        } else {
            $id = $model->id ? $model->id : $model->data[$model->alias]['id'];
            $aro = $model->Aro->node('first', [
                'conditions' => [
                    'model' => $model->alias,
                    'foreign_key' => $id,
                    ]
                ]);
            if (!empty($aro['Aro']['foreign_key'])) {
                $return = [
                    $aro[0]['Aro']['model'] => [
                        'id' => $aro['Aro']['foreign_key']
                    ]];
            } else {
                $return = null;
            }
            return $return;
        }
    }

/**
 * afterSave
 *
 * Update the corresponding ACO record alias
 */
    public function afterSave(Event $event, Entity $entity)
    {
        $model = $event->subject();
        $ref = ['model' => $model->alias(), 'foreign_key' => $entity->id];
        $aro = $model->node($ref)->firstOrFail();
        if (!empty($entity->alias)) {
            $aro->alias = sprintf(
                'Role-%s',
                Inflector::slug($entity->alias)
            );
        }
        if (!empty($entity->parent_id)) {
            $aro->parent_id = $entity->parent_id;
        }
        $model->Aro->save($aro);
        Cache::clearGroup('acl', 'permissions');
    }

/**
 * findRoleHierarchy
 *
 * binds Aro model so that it gets retrieved during admin_[edit|add].
 */
    public function findRoleHierarchy(Query $query, array $options)
    {

        $alias = $this->_table->alias();
        $primaryKey = $this->_table->primaryKey();
        $this->_table->hasOne('ParentAro', [
            'className' => 'Aros',
            'bindingKey' => 'id',
            'foreignKey' => 'foreign_key',
            'conditions' => [
                'model' => $alias,
            ],
        ]);

        $query
            ->contain('ParentAro')
            ->formatResults(function($resultSet) {
                foreach ($resultSet as $result) {
                    if ($result->parent_aro) {
                        $result->parent_id = $result->parent_aro->parent_id;
                        $result->lft = $result->parent_aro->lft;
                        $result->rght = $result->parent_aro->rght;
                        $result->setDirty('parent_id', false);
                        $result->setDirty('lft', false);
                        $result->setDirty('rght', false);
                        $result->unsetProperty('parent_aro');
                    }
                }
                return $resultSet;
            });
        return $query;
    }

/**
 * afterFind
 *
 * When 'parent_id' is present, copy its value from Aro to Role data.
 */
    public function afterFind(Model $model, $results, $primary = false)
    {
        if (!empty($results[0]['Aro']['parent_id'])) {
            $results[0][$model->alias]['parent_id'] = $results[0]['Aro']['parent_id'];
            return $results;
        }
    }

/**
 * Retrieve a list of allowed parent roles
 *
 * @paraam integer $roleId
 * @param int $id Role id
 * @return array list of allowable parent roles in 'list' format
 */
    public function allowedParents($id = null)
    {
        if (!$this->_table->behaviors()->has('Croogo/Core.Aliasable')) {
            $this->_table->addBehavior('Croogo/Core.Aliasable');
        }
        if ($id == $this->_table->byAlias('public')) {
            return [];
        }
        $adminRoleId = $this->_table->byAlias('superadmin');
        $excludes = Hash::filter(array_values([$adminRoleId, $id]));
        $conditions = [
            'NOT' => [$this->_table->aliasField('id') . ' IN'=> $excludes],
        ];
        return $this->_table->find('list')
            ->where($conditions)
            ->toArray();
    }

/**
 * afterDelete
 */
    public function afterDelete(Event $event)
    {
        Cache::clearGroup('acl', 'permissions');
    }
}
