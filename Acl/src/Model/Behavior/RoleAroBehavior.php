<?php

namespace Croogo\Acl\Model\Behavior;

use Cake\Cache\Cache;
use Cake\ORM\Behavior;
use Cake\Event\Event;
use Cake\ORM\Entity;
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
 * bindAro
 *
 * binds Aro model so that it gets retrieved during admin_[edit|add].
 */
    public function bindAro(Model $model)
    {
        $model->bindModel([
            'hasOne' => [
                'Aro' => [
                    'foreignKey' => false,
                    'conditions' => [
                        sprintf("model = '%s'", $model->alias),
                        sprintf('foreign_key = %s.%s', $model->alias, $model->primaryKey),
                    ],
                ],
            ],
        ], false);
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
 * @param int$idRole id
 * @return array list of allowable parent roles in 'list' format
 */
    public function allowedParents(Model $model, $id = null)
    {
        if (!$model->Behaviors->enabled('Croogo.Aliasable')) {
            $model->Behaviors->load('Croogo.Aliasable');
        }
        if ($id == $model->byAlias('public')) {
            return [];
        }
        $adminRoleId = $model->byAlias('admin');
        $excludes = Hash::filter(array_values([$adminRoleId, $id]));
        $options = ['conditions' => [
            'NOT' => [$model->alias . '.id' => $excludes],
        ]];
        return $model->find('list', $options);
    }

/**
 * afterDelete
 */
    public function afterDelete(Event $event)
    {
        Cache::clearGroup('acl', 'permissions');
    }
}
