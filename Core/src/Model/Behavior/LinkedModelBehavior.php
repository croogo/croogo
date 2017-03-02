<?php

namespace Croogo\Core\Model\Behavior;

use Cake\Database\Exception;
use Cake\Error\Debugger;
use Cake\Log\LogTrait;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

class LinkedModelBehavior extends Behavior
{
    use LogTrait;

    protected $_defaultConfig = [
        'modelField' => 'model',
        'foreignKeyField' => 'foreign_key'
    ];

    public function findRelatedEntity(Query $query)
    {
        $query->formatResults(function (ResultSet $resultSet) {
            return $resultSet->map(function (Entity $entity) {
                try {
                    $entity->related = $this->relatedTable($entity)->get($entity->get($this->config('foreignKeyField')));
                } catch (Exception $e) {
                    $this->log(Debugger::trace());
                }

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
