<?php

namespace Croogo\Core\Model\Behavior;

use Acl\Model\Behavior\AclBehavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * CroogoAcl Behavior
 *
 * @category Behavior
 * @package  Croogo.Croogo.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class CroogoAclBehavior extends AclBehavior
{

/**
 * setup
 *
 * @param Model $table
 * @param array $config
 */
    public function __construct(Table $table, array $config = [])
    {
        parent::__construct($table, $config);

        if (isset($config[0])) {
            $config['type'] = $config[0];
            unset($config[0]);
        }

        $this->config($table->alias(), array_merge(['type' => 'controlled'], $config));
        $this->config($table->alias() . '.type', strtolower($this->config($table->alias() . '.type')));

        $types = $this->_typeMaps[$this->config($table->alias() . '.type')];

        if (!is_array($types)) {
            $types = [$types];
        }

        foreach ($types as $type) {
            $alias = Inflector::pluralize($type);
            $table->hasOne($alias);
        }
    }
}
