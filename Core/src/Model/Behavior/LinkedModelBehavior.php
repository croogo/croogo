<?php

namespace Croogo\Core\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

class LinkedModelBehavior extends Behavior
{
    protected $_defaultConfig = [
        'modelField' => 'model',
        'foreignKeyField' => 'foreign_key'
    ];

    public function findRelatedEntity(Query $query)
    {
        $query->formatResults(function (ResultSet $resultSet) {
            return $resultSet->map(function (Entity $entity) {
                $entity->related = $this->relatedTable($entity)->get($entity->get($this->config('foreignKeyField')));

                return $entity;
            });
        });

        return $query;
    }

    /**
     * @param Entity $comment
     * @return \Cake\ORM\Table
     */
    public function relatedTable(Entity $comment)
    {
        return TableRegistry::get($comment->get($this->config('modelField')));
    }
}
