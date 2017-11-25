<?php

namespace Croogo\Acl\Model\Table;

use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;

/**
 * AclAco Model
 *
 * @category Model
 * @package  Croogo.Acl.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AcosTable extends \Acl\Model\Table\AcosTable
{

/**
 * getChildren
 *
 * @param integer aco id
 */
    public function getChildren($acoId, $fields = [])
    {
        $fields = Hash::merge(['id', 'parent_id', 'alias'], $fields);
        $acos = $this->find('children', ['for' => $acoId])
            ->find('threaded');
        return $acos;
    }

/**
 * Create ACO tree
 */
    public function createFromPath($path)
    {
        $pathE = explode('/', $path);
        $parent = $current = null;
        foreach ($pathE as $alias) {
            $current[] = $alias;
            $node = $this->node(join('/', $current));
            if ($node) {
                $node = $node->toArray();
                $parent = $node[0];
            } else {
                if (!$parent) {
                    $parent = $this->find()
                        ->where([
                            $this->aliasField('alias') => 'controllers',
                        ])
                        ->first();

                    if (!$parent) {
                        $rootNode = $this->newEntity([
                            'alias' => 'controllers',
                        ]);
                        $parent = $this->save($rootNode);
                    }
                }
                $aco = $this->newEntity([
                    'parent_id' => $parent->id,
                    'alias' => $alias,
                ]);
                $parent = $this->save($aco);
            }
        }
        return $parent;
    }

/**
 * ACL: add ACO
 *
 * Creates ACOs with permissions for roles.
 *
 * @param string $action possible values: Controller, Controller/action,
 *                                        Plugin/Controller/action
 * @param array $allowRoles Role aliases
 * @return void
 */
    public function addAco($action, $allowRoles = [])
    {
        // AROs
        $roles = [];
        if (count($allowRoles) > 0) {
            $roles = TableRegistry::get('Croogo/Users.Roles')->find('list', [
                'conditions' => [
                    'Roles.alias IN' => $allowRoles,
                ],
                'fields' => [
                    'Roles.id',
                    'Roles.alias',
                ],
            ])->toArray();
        }

        $this->createFromPath($action);
        $Permission = TableRegistry::get('Croogo/Acl.Permissions');
        foreach ($roles as $roleId => $roleAlias) {
            $Permission->allow(['model' => 'Roles', 'foreign_key' => $roleId], $action);
        }
    }

/**
 * ACL: remove ACO
 *
 * Removes ACOs and their Permissions
 *
 * @param string $action possible values: ControllerName, ControllerName/method_name
 * @return void
 */
    public function removeAco($action)
    {
        $acoNodes = $this->node($action);
        if ($acoNodes) {
            $acoNode = $acoNodes->first();
            $this->delete($acoNode);
        }
    }

/**
 * Get valid permission roots
 *
 * @return array Array of valid permission roots
 */
    public function getPermissionRoots()
    {
        $roots = $this->find('all', [
            'fields' => ['id', 'alias'],
            'conditions' => [
                'parent_id IS' => null,
                'alias IN' => ['controllers', 'api'],
            ],
        ])->toArray();

        $apiRoot = -1;
        foreach ($roots as $i => &$root) {
            if ($root->alias === 'api') {
                $apiRoot = $root->id;
                $apiIndex = $i;
            }
            $root->title = ucfirst($root->alias);
        }
        if (isset($apiIndex)) {
            unset($roots[$apiIndex]);
        }

        $versionRoots = $this->find('all', [
            'fields' => ['id', 'alias'],
            'conditions' => [
                'parent_id' => $apiRoot,
            ],
        ])->toArray();

        $apiCount = count($versionRoots);

        $api = __d('croogo', 'API');
        foreach ($versionRoots as &$versionRoot) {
            $alias = strtolower(str_replace('_', '.', $versionRoot->alias));
            $versionRoot->alias = $alias;
            $versionRoot->title = $apiCount == 1 ? $api : $api . ' ' . $alias;
        }

        return array_merge($roots, $versionRoots);
    }
}
