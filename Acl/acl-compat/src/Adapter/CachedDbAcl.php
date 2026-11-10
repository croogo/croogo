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

namespace Acl\Adapter;

use Acl\AclInterface;
use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

/**
 * CachedDbAcl extends DbAcl to add caching of permissions.
 *
 * Its usage is identical to that of DbAcl, however it supports a `Acl.cacheConfig` configuration value
 * This configuration value tells CachedDbAcl what cache config should be used.
 */
class CachedDbAcl extends DbAcl implements AclInterface
{

    protected $_cacheConfig = 'default';

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        if (Configure::check('Acl.cacheConfig')) {
            $this->_cacheConfig = Configure::read('Acl.cacheConfig');
        }
    }

    /**
     * {{@inheritDoc}}
     */
    public function check($aro, $aco, $action = "*")
    {
        $key = $this->_getCacheKey($aro, $aco, $action);

        $permission = Cache::remember($key, function () use ($aro, $aco, $action) {
            return $this->Permission->check($aro, $aco, $action) === true ? 'true' : 'false';
        }, $this->_cacheConfig);

        return $permission === 'true';
    }

    /**
     * {{@inheritDoc}}
     */
    public function allow($aro, $aco, $actions = "*", $value = 1)
    {
        Cache::clear($this->_cacheConfig);

        return parent::allow($aro, $aco, $actions, $value);
    }

    /**
     * Generates a string cache key for the ARO, ACO pair
     *
     * @param string|array|Entity $aro The requesting object identifier.
     * @param string|array|Entity $aco The controlled object identifier.
     * @param string $action Action
     * @return string
     */
    protected function _getCacheKey($aro, $aco, $action = '*')
    {
        return strtolower($this->_getNodeCacheKey($aro) . '_' . $this->_getNodeCacheKey($aco) . ($action == '*' ? '' : '_' . $action));
    }

    /**
     * Generates a key string to use for the cache
     *
     * @param string|array|Entity $ref Array with 'model' and 'foreign_key', model object, or string value
     * @return string
     */
    protected function _getNodeCacheKey($ref)
    {
        if (empty($ref)) {
            return '';
        } elseif (is_string($ref)) {
            return Text::slug($ref, '_');
        } elseif (is_object($ref) && $ref instanceof Entity) {
            return $ref->getSource() . '_' . $ref->id;
        } elseif (is_array($ref) && !(isset($ref['model']) && isset($ref['foreign_key']))) {
            $name = key($ref);
            list(, $alias) = pluginSplit($name);

            $bindTable = TableRegistry::getTableLocator()->get($name);
            $entityClass = $bindTable->getEntityClass();

            if ($entityClass) {
                $entity = new $entityClass();
            }

            if (empty($entity)) {
                throw new CakeException(
                    __d(
                        'cake_dev',
                        "Entity class {0} not found in CachedDbAcl::_getNodeCacheKey() when trying to bind {1} object",
                        [$type, $this->alias()]
                    )
                );
            }

            $tmpRef = null;
            if (method_exists($entity, 'bindNode')) {
                $tmpRef = $entity->bindNode($ref);
            }

            if (empty($tmpRef)) {
                $ref = [
                    'model' => $alias,
                    'foreign_key' => $ref[$name][$bindTable->getPrimaryKey()],
                ];
            } else {
                $ref = $tmpRef;
            }

            return $ref['model'] . '_' . $ref['foreign_key'];
        } elseif (is_array($ref)) {
            return $ref['model'] . '_' . $ref['foreign_key'];
        }

        return '';
    }
}
