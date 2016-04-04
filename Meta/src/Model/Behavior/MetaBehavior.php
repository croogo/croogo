<?php

namespace Croogo\Meta\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Meta Behavior
 *
 * @category Behavior
 * @package  Croogo.Meta.Model.Behavior
 * @version  1.0
 * @author   Fahad Ibnay Heylaal <contact@fahad19.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link     http://www.croogo.org
 */
class MetaBehavior extends Behavior
{

    /**
     * Setup
     *
     * @param Model $model
     * @param array $config
     * @return void
     */
    public function initialize(array $config = [])
    {
        parent::initialize($config);

        $this->_table->hasMany('Meta', [
            'className' => 'Croogo/Meta.Meta',
            'foreignKey' => 'foreign_key',
            'dependent' => true,
            'conditions' => [
                'Meta.model' => $this->_table->alias(),
            ],
            'order' => 'Meta.key ASC',
        ]);

        TableRegistry::get('Croogo/Meta.Meta')
            ->belongsTo($this->_table->alias(), [
                'targetTable' => $this->_table,
                'foreignKey' => 'foreign_key',
                'conditions' => [
                    'Meta.model' => $this->_table->alias(),
                ],
            ]);
    }

    /**
     * afterFind callback
     *
     * @return array
     */
    public function beforeFind(Event $event, Query $query)
    {
        $query
            ->contain(['Meta'])
            ->formatResults(function ($resultSet) {
                return $resultSet->map(function (Entity $entity) {
                    $customFields = [];
                    if (!empty($entity->meta)) {
                        $customFields = Hash::combine($entity->meta, '{n}.key', '{n}.value');
                    }
                    $entity->custom_fields = $customFields;
                    return $entity;
                });
            });

        return $query;
    }

    /**
     * Prepare data
     *
     * @param Model $model
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        return $this->_prepareMeta($data);
    }

    /**
     * Protected method for MetaBehavior::prepareData()
     *
     * @param Model $model
     * @param array $data
     * @return array
     */
    protected function _prepareMeta(\ArrayObject $data, \ArrayObject $options)
    {
        if (isset($data['meta']) &&
            is_array($data['meta']) &&
            count($data['meta']) > 0 &&
            !Hash::numeric(array_keys($data['meta']))
        ) {
            $meta = $data['meta'];
            $data['meta'] = [];
            $i = 0;
            foreach ($meta as $metaArray) {
                $data['meta'][$i] = $metaArray;
                $i++;
            }

            if (isset($options['associated']) && !(isset($options['associated']['meta']) || in_array('meta', $options['associated']))) {
                $options['associated'][] = 'meta';
            }
        }
    }

    /**
     * Handle Model.beforeMarshal event
     *
     * @param Event $event Event object
     * @return void
     */
    public function beforeMarshal($event)
    {
        $this->_prepareMeta($event->data['data'], $event->data['options']);
    }
}
