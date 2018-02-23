<?php

namespace Croogo\Settings\Model\Entity;

use Cake\Core\App;
use Cake\ORM\Entity;

class Language extends Entity
{

    protected function _getLabel()
    {
        return $this->_properties['native'] ?: $this->_properties['title'];
    }

}
