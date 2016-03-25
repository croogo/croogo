<?php

namespace Croogo\Settings\Model\Entity;

use Cake\Core\App;
use Cake\ORM\Entity;

class Setting extends Entity
{
    protected function _getOptions()
    {
        if ($this->has('option_class')) {
            $className = App::className($this->option_class, 'Setting', 'Setting');
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
