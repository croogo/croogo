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

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Acl\Model\Table\ArosTable;
use Acl\Model\Table\AcosTable;

/**
 * Permissions linking AROs with ACOs
 *
 */
class PermissionsTable extends AclNodesTable
{
    private ArosTable $Aro;
    private AcosTable $Aco;

    /**
     * {@inheritDoc}
     *
     * @param array $config Configuration
     * @return void
     */
    public function initialize(array $config) :void
    {
        $this->setAlias('Permissions');
        $this->setTable('aros_acos');
        $this->belongsTo('Aros', [
            'className' => App::className('Acl.ArosTable', 'Model/Table'),
        ]);
        $this->belongsTo('Acos', [
            'className' => App::className('Acl.AcosTable', 'Model/Table'),
        ]);
        $this->Aro = $this->Aros->getTarget();
        $this->Aco = $this->Acos->getTarget();
    }

    /**
     * Checks if the given $aro has access to action $action in $aco
     *
     * @param string $aro ARO The requesting object identifier.
     * @param string $aco ACO The controlled object identifier.
     * @param string $action Action (defaults to *)
     * @return bool Success (true if ARO has access to action in ACO, false otherwise)
     */
    public function check($aro, $aco, $action = '*')
    {
        if (!$aro || !$aco) {
            return false;
        }

        $permKeys = $this->getAcoKeys($this->getSchema()->columns());
        $aroPath = $this->Aro->node($aro);
        $acoPath = $this->Aco->node($aco);

        if (!$aroPath) {
            trigger_error(
                __d(
                    'cake_dev',
                    "{0} - Failed ARO node lookup in permissions check. Node references:\nAro: {1}\nAco: {2}",
                    'DbAcl::check()',
                    print_r($aro, true),
                    print_r($aco, true)
                ),
                E_USER_WARNING
            );

            return false;
        }

        if (!$acoPath) {
            trigger_error(
                __d(
                    'cake_dev',
                    "{0} - Failed ACO node lookup in permissions check. Node references:\nAro: {1}\nAco: {2}",
                    'DbAcl::check()',
                    print_r($aro, true),
                    print_r($aco, true)
                ),
                E_USER_WARNING
            );

            return false;
        }

        if ($action !== '*' && !in_array('_' . $action, $permKeys)) {
            trigger_error(__d('cake_dev', "ACO permissions key {0} does not exist in {1}", $action, 'DbAcl::check()'), E_USER_NOTICE);

            return false;
        }

        $inherited = [];
        $acoIDs = $acoPath->all()->extract('id')->toArray();

        $count = $aroPath->count();
        $aroPaths = $aroPath->toArray();
        for ($i = 0; $i < $count; $i++) {
            $permAlias = $this->getAlias();

            $perms = $this->find('all',
            conditions: [
                "{$permAlias}.aro_id" => $aroPaths[$i]->id,
                "{$permAlias}.aco_id IN" => $acoIDs,
            ],
            order: [$this->Aco->getAlias() . '.lft' => 'desc'],
            contain: $this->Aco->getAlias());

            if ($perms->count() == 0) {
                continue;
            }
            $perms = $perms->enableHydration(false)->toArray();
            foreach ($perms as $perm) {
                if ($action === '*') {
                    foreach ($permKeys as $key) {
                        if (!empty($perm)) {
                            if ($perm[$key] == -1) {
                                return false;
                            } elseif ($perm[$key] == 1) {
                                $inherited[$key] = 1;
                            }
                        }
                    }

                    if (count($inherited) === count($permKeys)) {
                        return true;
                    }
                } else {
                    switch ($perm['_' . $action]) {
                        case -1:
                            return false;
                        case 0:
                            break;
                        case 1:
                            return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Allow $aro to have access to action $actions in $aco
     *
     * @param string $aro ARO The requesting object identifier.
     * @param string $aco ACO The controlled object identifier.
     * @param string $actions Action (defaults to *) Invalid permissions will result in an exception
     * @param int $value Value to indicate access type (1 to give access, -1 to deny, 0 to inherit)
     * @return bool Success
     * @throws \Cake\Core\Exception\Exception on Invalid permission key.
     */
    public function allow($aro, $aco, $actions = '*', $value = 1)
    {
        $perms = $this->getAclLink($aro, $aco);
        $permKeys = $this->getAcoKeys($this->getSchema()->columns());
        $alias = $this->getAlias();
        $save = [];

        if (!$perms) {
            trigger_error(__d('cake_dev', '{0} - Invalid node', ['DbAcl::allow()']), E_USER_WARNING);

            return false;
        }
        if (isset($perms[0])) {
            $save = $perms[0][$alias];
        }

        if ($actions === '*') {
            $save = array_combine($permKeys, array_pad([], count($permKeys), $value));
        } else {
            if (!is_array($actions)) {
                if (substr($actions, 0, 1) !== '_') {
                    $actions = ['_' . $actions];
                } else {
                    $actions = [$actions];
                }
            }
            foreach ($actions as $action) {
                if (substr($action, 0, 1) !== '_') {
                    $action = '_' . $action;
                }
                if (!in_array($action, $permKeys, true)) {
                    throw new CakeException(__d('cake_dev', 'Invalid permission key "{0}"', [$action]));
                }
                $save[$action] = $value;
            }
        }
        list($save['aro_id'], $save['aco_id']) = [$perms['aro'], $perms['aco']];

        if ($perms['link'] && !empty($perms['link'][$alias])) {
            $save['id'] = $perms['link'][$alias][0]['id'];
        } else {
            unset($save['id']);
            $this->id = null;
        }
        $entityClass = $this->getEntityClass();
        $entity = new $entityClass($save);

        return ($this->save($entity) !== false);
    }

    /**
     * Get an array of access-control links between the given Aro and Aco
     *
     * @param string $aro ARO The requesting object identifier.
     * @param string $aco ACO The controlled object identifier.
     * @return array Indexed array with: 'aro', 'aco' and 'link'
     */
    public function getAclLink($aro, $aco)
    {
        $obj = [];
        $obj['Aro'] = $this->Aro->node($aro);
        $obj['Aco'] = $this->Aco->node($aco);

        if (empty($obj['Aro']) || empty($obj['Aco'])) {
            return false;
        }
        $aro = $obj['Aro']->all()->extract('id')->toArray();
        $aco = $obj['Aco']->all()->extract('id')->toArray();
        $aro = current($aro);
        $aco = current($aco);
        $alias = $this->getAlias();

        $result = [
            'aro' => $aro,
            'aco' => $aco,
            'link' => [
                $alias => $this->find('all', conditions: [
                    $alias . '.aro_id' => $aro,
                    $alias . '.aco_id' => $aco,
                ])->enableHydration(false)->toArray(),
            ],
        ];

        return $result;
    }

    /**
     * Get the crud type keys
     *
     * @param array $keys Permission schema
     * @return array permission keys
     */
    public function getAcoKeys($keys)
    {
        $newKeys = [];
        foreach ($keys as $key) {
            if (!in_array($key, ['id', 'aro_id', 'aco_id'])) {
                $newKeys[] = $key;
            }
        }

        return $newKeys;
    }
}

