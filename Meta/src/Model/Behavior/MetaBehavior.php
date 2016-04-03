<?php

namespace Croogo\Meta\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

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
        ], false);

        TableRegistry::get('Croogo/Meta.Meta')
            ->belongsTo($this->_table->alias(), [
                'targetTable' => $this->_table,
                'foreignKey' => 'foreign_key'
            ]);
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        $implementedEvents = parent::implementedEvents();

        $implementedEvents['Model.Node.beforeSaveNode'] = 'onBeforeSaveNode';

        return $implementedEvents;
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
    public function prepareData(Model $model, $data)
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
    protected function _prepareMeta($data)
    {
        if (isset($data['Meta']) &&
            is_array($data['Meta']) &&
            count($data['Meta']) > 0 &&
            !Hash::numeric(array_keys($data['Meta']))
        ) {
            $meta = $data['Meta'];
            $data['Meta'] = [];
            $i = 0;
            foreach ($meta as $metaArray) {
                $data['Meta'][$i] = $metaArray;
                $i++;
            }
        }

        return $data;
    }

    /**
     * Handle Model.Node.beforeSaveNode event
     *
     * @param Event $event
     */
    public function onBeforeSaveNode($event)
    {
        $event->data['data'] = $this->_prepareMeta($event->data['data']);

        return true;
    }

    /**
     * Save with meta
     *
     * @param Model $model
     * @param array $data
     * @param array $options
     * @return void
     * @deprecated Use standard Model::saveAll()
     */
    public function saveWithMeta(Model $model, $data, $options = [])
    {
        $data = $this->_prepareMeta($data);

        return $model->saveAll($data, $options);
    }
}
