<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Acl\Model\Table;

use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

/**
 * ACL Nodes
 *
 * PHP 8.2+: kod legacy (Cake 2-style) ustawia dynamiczne właściwości
 * (np. $this->id w PermissionsTable) - stąd AllowDynamicProperties.
 */
#[\AllowDynamicProperties]
class AclNodesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public static function defaultConnectionName() :string
    {
        return Configure::read('Acl.database');
    }

    /**
     * Retrieves the Aro/Aco node for this model
     *
     * @param string|array|Table $ref Array with 'model' and 'foreign_key', model object, or string value
     * @return array|\Cake\ORM\Query\SelectQuery|false Node found in database
     * @throws \Cake\Core\Exception\Exception when binding to a model that doesn't exist.
     */
    public function node($ref = null)
    {
        $type = $this->getAlias();
        $table = $this->getTable();
        $result = null;
        $query = false;

        if (empty($ref)) {
            return null;
        } elseif (is_int($ref) || (is_string($ref) && ctype_digit($ref))) {
            $ref = [
                'id' => $ref,
            ];
        } elseif (is_string($ref)) {
            $path = explode('/', $ref);
            $start = $path[0];
            unset($path[0]);

            $queryData = [
                'conditions' => [
                    "{$type}.lft" . ' <= ' => new IdentifierExpression("{$type}0.lft"),
                    "{$type}.rght" . ' >= ' => new IdentifierExpression("{$type}0.rght"),
                ],
                'fields' => ['id', 'parent_id', 'model', 'foreign_key', 'alias'],
                'join' => [[
                        'table' => $table,
                        'alias' => "{$type}0",
                        'type' => 'INNER',
                        'conditions' => ["{$type}0.alias" => $start],
                ]],
                'order' => ["{$type}.lft" => 'DESC'],
            ];

            foreach ($path as $i => $alias) {
                $j = $i - 1;

                $queryData['join'][] = [
                    'table' => $table,
                    'alias' => "{$type}{$i}",
                    'type' => 'INNER',
                    'conditions' => [
                        "{$type}{$i}.lft" . ' > ' => new IdentifierExpression("{$type}{$j}.lft"),
                        "{$type}{$i}.rght" . ' < ' => new IdentifierExpression("{$type}{$j}.rght"),
                        "{$type}{$i}.alias" => $alias,
                        "{$type}{$j}.id" . ' = ' => new IdentifierExpression("{$type}{$i}.parent_id"),
                    ],
                ];

                $queryData['conditions'] = [
                    'or' => [
                        ["{$type}.lft" . ' <= ' => new IdentifierExpression("{$type}0.lft"), "{$type}.rght" . ' >= ' => new IdentifierExpression("{$type}0.rght")],
                        ["{$type}.lft" . ' <= ' => new IdentifierExpression("{$type}{$i}.lft"), "{$type}.rght" . ' >= ' => new IdentifierExpression("{$type}{$i}.rght")],
                    ],
                ];
            }
            // Cake 5: pozycyjna tablica opcji do find() deprecated -> named args
            $query = $this->find(
                'all',
                conditions: $queryData['conditions'],
                fields: $queryData['fields'],
                join: $queryData['join'],
                order: $queryData['order']
            );
            $result = $query->toArray();
            $path = array_values($path);

            if (
                !isset($result[0]) ||
                (!empty($path) && $result[0]->alias != $path[count($path) - 1]) ||
                (empty($path) && $result[0]->alias != $start)
            ) {
                return false;
            }
        } elseif (is_object($ref) && $ref instanceof Entity) {
            list(, $alias) = pluginSplit($ref->getSource());
            $ref = ['model' => $alias, 'foreign_key' => $ref->id];
        } elseif (is_array($ref) && !(isset($ref['model']) && isset($ref['foreign_key']))) {
            $name = key($ref);
            list(, $alias) = pluginSplit($name);

            if (TableRegistry::getTableLocator()->exists($name)) {
                $bindTable = TableRegistry::getTableLocator()->get($name);
            } else {
                $connection = Configure::read('Acl.database');
                $bindTable = TableRegistry::getTableLocator()->get($name, [
                    'connection' => ConnectionManager::get($connection),
                ]);
            }
            $entityClass = $bindTable->getEntityClass();

            if ($entityClass) {
                $entity = new $entityClass();
            }

            if (empty($entity)) {
                throw new CakeException(__d('cake_dev', "Entity class {0} not found in AclNode::node() when trying to bind {1} object", [$type, $this->getAlias()]));
            }

            $tmpRef = null;
            if (method_exists($entity, 'bindNode')) {
                $tmpRef = $entity->bindNode($ref);
            }
            if (empty($tmpRef)) {
                $ref = [
                    'model' => $alias,
                    'foreign_key' => $ref[$name][$this->getPrimaryKey()],
                ];
            } else {
                if (is_string($tmpRef)) {
                    return $this->node($tmpRef);
                }
                $ref = $tmpRef;
            }
        }
        if (is_array($ref)) {
            if (is_array(current($ref)) && is_string(key($ref))) {
                $name = key($ref);
                $ref = current($ref);
            }
            foreach ($ref as $key => $val) {
                if (strpos($key, $type) !== 0 && strpos($key, '.') === false) {
                    unset($ref[$key]);
                    $ref["{$type}0.{$key}"] = $val;
                }
            }
            $queryData = [
                'conditions' => $ref,
                'fields' => ['id', 'parent_id', 'model', 'foreign_key', 'alias'],
                'join' => [
                    [
                        'table' => $table,
                        'alias' => "{$type}0",
                        'type' => 'INNER',
                        'conditions' => [
                            "{$type}.lft" . ' <= ' => new IdentifierExpression("{$type}0.lft"),
                            "{$type}.rght" . ' >= ' => new IdentifierExpression("{$type}0.rght"),
                        ],
                    ],
                ],
                'order' => ["{$type}.lft" => 'DESC'],
            ];
            $query = $this->find(
                'all',
                conditions: $queryData['conditions'],
                fields: $queryData['fields'],
                join: $queryData['join'],
                order: $queryData['order']
            );

            if ($query->count() == 0) {
                throw new CakeException(__d('cake_dev', "AclNode::node() - Couldn't find %s node identified by \"%s\"", [$type, print_r($ref, true)]));
            }
        }

        return $query;
    }
}
