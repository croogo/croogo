<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;

/**
 * Aliasable Behavior
 *
 * Utility behavior to allow easy retrieval of records by id or its alias
 *
 * @package  Croogo.Croogo.Model.Behavior
 * @since    1.4
 * @author   Rachman Chavik <rchavik@xintesa.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class AliasableBehavior extends Behavior
{

    protected $_defaultConfig = [
        'id' => 'id',
        'alias' => 'alias',
    ];

/**
 * _byIds
 *
 * @var array
 */
    protected $_byIds = [];

/**
 * _byAlias
 *
 * @var array
 */
    protected $_byAlias = [];

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->reload();
    }


    /**
     * reload
     *
     * @return void
     */
    public function reload()
    {
        $this->_byIds = $this->_table
            ->find('list', [
                'keyField' => $this->config('id'),
                'valueField' => $this->config('alias'),
            ])
            ->where([
                $this->_table->aliasField($this->config('alias')) . ' !=' => '',
            ])
            ->toArray();
        $this->_byAlias = array_flip($this->_byIds);
    }

/**
 * byId
 *
 * @param
 * @param int $id
 * @return boolean
 */
    public function byId($id)
    {
        if (!empty($this->_byIds[$id])) {
            return $this->_byIds[$id];
        }
        return false;
    }

/**
 * byAlias
 *
 * @param string $alias
 * @return boolean
 */
    public function byAlias($alias)
    {
        if (!empty($this->_byAlias[$alias])) {
            return $this->_byAlias[$alias];
        }
        return false;
    }

/**
 * listById
 *
 * @return string
 */
    public function listById()
    {
        return $this->_byIds;
    }

/**
 * listByAlias
 *
 * @param
 * @return string
 */
    public function listByAlias()
    {
        return $this->_byAlias;
    }
}
