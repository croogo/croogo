<?php
declare(strict_types=1);

namespace Croogo\Settings\Model\Entity;

use Cake\ORM\Entity;

class Language extends Entity
{

    protected function _getLabel()
    {
        return $this->native ?: $this->title;
    }
}
