<?php

namespace Croogo\Users\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class User extends Entity
{

    protected $_hidden = ['password', 'activation_key'];

    /**
     * Hashes password when setting
     *
     * @param string $password
     * @return bool|string
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    /**
     * parentNode
     *
     * @return array
     */
    public function parentNode()
    {
        if (!$this->id) {
            return null;
        }
        if (empty($this->get('role_id'))) {
            return null;
        } else {
            return ['Roles' => ['id' => $this->get('role_id')]];
        }
    }
}
