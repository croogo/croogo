<?php

namespace Croogo\Acl\Model\Table;

use Cake\Cache\Cache;
use Cake\Utility\Hash;

/**
 * AclPermission Model
 *
 * @category Model
 * @package  Croogo.Acl.Model
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class PermissionsTable extends \Acl\Model\Table\PermissionsTable
{

    /**
     * afterSave
     */
    public function afterSave(\Cake\Event\EventInterface $created, $options = []): void
    {
        Cache::clearGroup('acl', 'permissions');
    }

    /**
     * Retrieve an array for formatted aros/aco data
     *
     * @param array $acos
     * @param array $aros
     * @param array $options
     * @return array formatted array
     */
    public function format($acos, $aros, $options = [])
    {
        $options = Hash::merge([
            'model' => 'Roles',
            'perms' => true
        ], $options);
        extract($options);
        $permissions = [];

        foreach ($acos as $aco) {
            $acoId = $aco->id;
            $acoAlias = $aco->alias;

            $path = $this->Acos->find('path', for: $acoId);
            $path = join('/', collection($path)->extract('alias')->toArray());
            $data = [
                // Cake 5.3: metody behaviora na instancji tabeli deprecated
                'children' => $this->Acos->getBehavior('Tree')->childCount($aco, true),
                'depth' => substr_count($path, '/'),
            ];

            foreach ($aros as $aroFk => $aroId) {
                $role = [
                    'model' => $model, 'foreign_key' => $aroFk,
                ];
                if ($perms) {
                    if ($aroFk == 1 || $this->check($role, $path)) {
                        $data['roles'][$aroFk] = 1;
                    } else {
                        $data['roles'][$aroFk] = 0;
                    }
                }
                $permissions[$acoId] = [$acoAlias => $data];
            }
        }

        return $permissions;
    }

    /**
     * The role verdicts format() gives, for many ACO paths at once
     *
     * format() runs check() per ACO and role, ~50 ms a pair on the unindexed acl tables (a minute
     * for 100 actions x 10 roles). This loads the tree and the permission rows once and replays
     * node() and check() in memory, role 1 always allowed as in format(); keep it in step with both.
     *
     * @param array<int, string> $acoPaths ACO paths keyed by ACO id
     * @param array<int, int> $aros ARO ids keyed by role id, as ArosTable::getRoles() returns them
     * @return array<int, array<int, int>> 1 (allowed) or 0, keyed by ACO id, then role id
     */
    public function roleVerdicts(array $acoPaths, array $aros): array
    {
        if (!$acoPaths || !$aros) {
            return [];
        }

        $nodes = $this->Acos->getTarget()->find()
            ->select(['id', 'parent_id', 'alias', 'lft', 'rght'])
            ->disableHydration()
            ->all()
            ->indexBy('id')
            ->toArray();
        $children = [];
        $byAlias = [];
        foreach ($nodes as $id => $node) {
            if ($node['parent_id'] !== null) {
                $children[$node['parent_id']][] = $id;
            }
            $byAlias[mb_strtolower($node['alias'])][] = $id;
        }

        $resolved = [];
        $acoIds = [];
        foreach ($acoPaths as $path) {
            if (!array_key_exists($path, $resolved)) {
                $resolved[$path] = $this->_resolveAcoPath($path, $nodes, $children, $byAlias);
                $acoIds += $resolved[$path] ?? [];
            }
        }

        $aroPaths = [];
        $aroIds = [];
        foreach (array_keys($aros) as $roleId) {
            $aroPath = $this->Aros->getTarget()->node(['model' => 'Roles', 'foreign_key' => $roleId]);
            $aroPaths[$roleId] = $aroPath ? collection($aroPath)->extract('id')->toList() : [];
            $aroIds = array_merge($aroIds, $aroPaths[$roleId]);
        }

        $permKeys = $this->getAcoKeys($this->getSchema()->columns());
        $perms = [];
        if ($acoIds && $aroIds) {
            $rows = $this->find()
                ->select(array_merge(['aro_id', 'aco_id'], $permKeys))
                ->where(['aro_id IN' => array_unique($aroIds), 'aco_id IN' => array_keys($acoIds)])
                ->disableHydration()
                ->all();
            foreach ($rows as $row) {
                $perms[$row['aro_id']][] = $row;
            }
        }

        $verdicts = [];
        foreach ($acoPaths as $acoId => $path) {
            foreach (array_keys($aros) as $roleId) {
                $allowed = $roleId == 1 || ($resolved[$path] !== null
                    && $this->_replayCheck($aroPaths[$roleId], $resolved[$path], $perms, $permKeys));
                $verdicts[$acoId][$roleId] = $allowed ? 1 : 0;
            }
        }

        return $verdicts;
    }

    /**
     * The ACO ids AclNodesTable::node() returns for a path, or null where it returns false
     *
     * Mirrors its query, damaged trees included: one row per segment (the first by alias alone,
     * the rest by alias, parent_id and a nested interval), every node whose interval holds the
     * first or last row of a match, failure when the deepest is not named after the last
     * segment; aliases compare case-insensitively, like the column's collation.
     *
     * @param string $path ACO path
     * @param array $nodes ACO rows keyed by id
     * @param array $children Child ids keyed by parent id
     * @param array $byAlias Ids keyed by lower-cased alias
     * @return array<int, int>|null lft keyed by ACO id, deepest first
     */
    protected function _resolveAcoPath(string $path, array $nodes, array $children, array $byAlias): ?array
    {
        $segments = explode('/', $path);
        $matches = [];
        foreach ($byAlias[mb_strtolower($segments[0])] ?? [] as $id) {
            $matches[] = [$id, $id];
        }
        foreach (array_slice($segments, 1) as $alias) {
            $next = [];
            foreach ($matches as [$first, $last]) {
                foreach ($children[$last] ?? [] as $childId) {
                    $child = $nodes[$childId];
                    if (
                        mb_strtolower($child['alias']) === mb_strtolower($alias)
                        && $child['lft'] > $nodes[$last]['lft']
                        && $child['rght'] < $nodes[$last]['rght']
                    ) {
                        $next[] = [$first, $childId];
                    }
                }
            }
            $matches = $next;
        }

        $found = [];
        foreach ($nodes as $id => $node) {
            foreach ($matches as $match) {
                foreach ($match as $anchor) {
                    if ($node['lft'] <= $nodes[$anchor]['lft'] && $node['rght'] >= $nodes[$anchor]['rght']) {
                        $found[$id] = $node['lft'];
                        continue 3;
                    }
                }
            }
        }
        arsort($found);
        if (!$found || $nodes[array_key_first($found)]['alias'] != end($segments)) {
            return null;
        }

        return $found;
    }

    /**
     * check() for one role and one resolved ACO path, over preloaded permission rows
     *
     * @param array<int> $aroIds The role's ARO path, as node() orders it
     * @param array<int, int> $acoLfts lft keyed by ACO id, from _resolveAcoPath()
     * @param array $perms Permission rows keyed by aro_id
     * @param array<string> $permKeys Permission columns
     * @return bool
     */
    protected function _replayCheck(array $aroIds, array $acoLfts, array $perms, array $permKeys): bool
    {
        $inherited = [];
        foreach ($aroIds as $aroId) {
            $rows = array_filter($perms[$aroId] ?? [], fn($row) => isset($acoLfts[$row['aco_id']]));
            usort($rows, fn($a, $b) => $acoLfts[$b['aco_id']] <=> $acoLfts[$a['aco_id']]);
            foreach ($rows as $row) {
                foreach ($permKeys as $key) {
                    if ($row[$key] == -1) {
                        return false;
                    } elseif ($row[$key] == 1) {
                        $inherited[$key] = 1;
                    }
                }
                if (count($inherited) === count($permKeys)) {
                    return true;
                }
            }
        }

        return false;
    }
}
