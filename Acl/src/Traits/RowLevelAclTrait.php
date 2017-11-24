<?php

namespace Croogo\Acl\Traits;

use Cake\ORM\TableRegistry;

trait RowLevelAclTrait
{

/**
 * parentNode
 *
 * @param $model Model model instance
 */
    public function parentNode()
    {
        //if (!$model->id && empty($model->data)) {
        if (!$this->id && !$this->isDirty()) {
            return null;
        } else {

/*
            $alias = $model->alias;
            if ($model->id) {
                $id = $model->id;
            } else {
                $id = $model->data[$alias][$model->primaryKey];
            }
*/

            $Table = TableRegistry::get($this->getSource());
            $alias = $this->getSource();

            $aco = $Table->Aco->find()
                ->where([
                    'model' => $alias,
                    'foreign_key' => $this->id,
                ])
                ->first();
            if (!$aco) {
                $return = 'contents';
            } else {
                $return = [
                    $alias => [
                        'id' => $aco->foreign_key,
                    ],
                ];
            }
            return $return;
        }
    }

}
