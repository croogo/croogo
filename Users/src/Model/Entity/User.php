<?php

namespace Croogo\Users\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class User extends Entity {

    protected function _setPassword($password) {
        return (new DefaultPasswordHasher)->hash($password);
    }
}
