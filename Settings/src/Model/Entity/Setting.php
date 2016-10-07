<?php

namespace Croogo\Settings\Model\Entity;

use Cake\Core\App;
use Cake\ORM\Entity;

class Setting extends Entity
{

    protected function _getOptions()
    {
        if (!empty($this->params['optionClass'])) {
            $className = App::className($this->params['optionClass'], 'Setting', 'Setting');
            if (!$className) {
                return [];
            }
            $class = new $className();
            return $class();
        } elseif (!empty($this->params['options'])) {
            return json_decode($this->params['options'], true);
        }
    }
}
