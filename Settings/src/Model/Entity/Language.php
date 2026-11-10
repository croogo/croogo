<?php

namespace Croogo\Settings\Model\Entity;

use Cake\ORM\Entity;

class Language extends Entity
{

    protected function _getLabel()
    {
        return $this->_fields['native'] ?: $this->_fields['title'];
    }
}
