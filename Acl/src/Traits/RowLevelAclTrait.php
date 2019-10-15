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
        if (!$this->id && !$this->isDirty()) {
            return null;
        } else {
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
