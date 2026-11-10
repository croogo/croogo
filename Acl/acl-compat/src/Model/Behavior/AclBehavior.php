<?php

/**
 * CakePHP :  Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Acl\Model\Behavior;

use Cake\Core\App;
use Cake\Core\Exception\CakeException;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * ACL behavior
 *
 * Enables objects to easily tie into an ACL system
 *
 * @link https://book.cakephp.org/2.0/en/core-libraries/behaviors/acl.html
 */
class AclBehavior extends Behavior
{

    /**
     * Maps ACL type options to ACL models
     *
     * @var array
     */
    protected $_typeMaps = ['requester' => 'Aro', 'controlled' => 'Aco', 'both' => ['Aro', 'Aco']];

    /**
     * Sets up the configuration for the model, and loads ACL models if they haven't been already
     *
     * @param Table $model Table instance being attached
     * @param array $config Configuration
     * @return void
     */
    public function __construct(Table $model, array $config = [])
    {
        $this->_table = $model;
        if (isset($config[0])) {
            $config['type'] = $config[0];
            unset($config[0]);
        }
        if (isset($config['type'])) {
            $config['type'] = strtolower($config['type']);
        }
        parent::__construct($model, $config);

        $types = $this->_typeMaps[$this->getConfig()['type']];

        if (!is_array($types)) {
            $types = [$types];
        }
        foreach ($types as $type) {
            $alias = Inflector::pluralize($type);
            $className = App::className($alias . 'Table', 'Model/Table');
            if ($className == false) {
                $className = App::className('Acl.' . $alias . 'Table', 'Model/Table');
            }
            $config = [];
            if (!TableRegistry::getTableLocator()->exists($alias)) {
                $config = ['className' => $className];
            }
            $model->hasMany($type, [
                'targetTable' => TableRegistry::getTableLocator()->get($alias, $config),
            ]);
        }

        if (!method_exists($model->getEntityClass(), 'parentNode')) {
            trigger_error(__d('cake_dev', 'Callback {0} not defined in {1}', ['parentNode()', $model->getEntityClass()]), E_USER_WARNING);
        }
    }

    /**
     * Retrieves the Aro/Aco node for this model
     *
     * @param string|array|Model $ref Array with 'model' and 'foreign_key', model object, or string value
     * @param string $type Only needed when Acl is set up as 'both', specify 'Aro' or 'Aco' to get the correct node
     * @return \Cake\ORM\Query\SelectQuery
     * @link https://book.cakephp.org/2.0/en/core-libraries/behaviors/acl.html#node
     * @throws \Cake\Core\Exception\Exception
     */
    public function node($ref = null, $type = null)
    {
        if (empty($type)) {
            $type = $this->_typeMaps[$this->getConfig('type')];
            if (is_array($type)) {
                trigger_error(__d('cake_dev', 'AclBehavior is setup with more then one type, please specify type parameter for node()'), E_USER_WARNING);

                return null;
            }
        }
        if (empty($ref)) {
            throw new CakeException(__d('cake_dev', 'ref parameter must be a string or an Entity'));
        }

        return $this->_table->{$type}->node($ref);
    }

    /**
     * Creates a new ARO/ACO node bound to this record
     *
     * @param Event $event The afterSave event that was fired
     * @param Entity $entity The entity being saved
     * @return void
     */
    public function afterSave(Event $event, Entity $entity)
    {
        $model = $event->getSubject();
        $types = $this->_typeMaps[$this->getConfig('type')];
        if (!is_array($types)) {
            $types = [$types];
        }
        foreach ($types as $type) {
            $parent = $entity->parentNode();
            if (!empty($parent)) {
                $parent = $this->node($parent, $type)->first();
            }
            $data = [
                'parent_id' => isset($parent->id) ? $parent->id : null,
                'model' => $model->getAlias(),
                'foreign_key' => $entity->id,
            ];

            if (method_exists($entity, 'nodeAlias')) {
                $data['alias'] = $entity->nodeAlias();
            }

            if (!$entity->isNew()) {
                $node = $this->node($entity, $type)->first();
                $data['id'] = isset($node->id) ? $node->id : null;
                $newData = $model->{$type}->patchEntity($node, $data);
            } else {
                $newData = $model->{$type}->newEntity($data);
            }

            $saved = $model->{$type}->getTarget()->save($newData);
        }
    }

    /**
     * Destroys the ARO/ACO node bound to the deleted record
     *
     * @param Event $event The afterDelete event that was fired
     * @param Entity $entity The entity being deleted
     * @return void
     */
    public function afterDelete(Event $event, Entity $entity)
    {
        $types = $this->_typeMaps[$this->getConfig('type')];
        if (!is_array($types)) {
            $types = [$types];
        }
        foreach ($types as $type) {
            $node = $this->node($entity, $type)->toArray();
            if (!empty($node)) {
                $event->getSubject()->{$type}->delete($node[0]);
            }
        }
    }
}
