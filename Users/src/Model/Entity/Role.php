<?php
namespace Croogo\Users\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Role extends Entity
{

    /**
     * parentNode
     *
     * @return $mixed
     */
    public function parentNode()
    {
        if (!$this->id) {
            return null;
        } else {
            $aro = TableRegistry::get('Croogo/Acl.Aros')->node('first', [
                'conditions' => [
                    'model' => $this->alias,
                    'foreign_key' => $this->id,
                ]
            ]);
            if (!$aro) {
                return null;
            }
            if (!empty($aro->get('foreign_key'))) {
                $return = [
                    $aro->get('model') => [
                        'id' => $aro->get('foreign_key')
                    ]
                ];
            } else {
                $return = null;
            }
            return $return;
        }
    }

    /**
     * @return string
     */
    public function nodeAlias()
    {
        return 'Role-' . $this->alias;
    }
}
