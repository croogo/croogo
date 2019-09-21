<?php

namespace Croogo\Taxonomy\Model\Entity;

use Cake\ORM\Entity;

class Taxonomy extends Entity
{

    protected $_virtual = ['title'];

    protected function _getTitle($title)
    {
        $titles = [
            isset($this->vocabulary->title)
                ? $this->vocabulary->title
                : $this->vocabulary_id,
            isset($this->term->title)
                ? $this->term->title
                : $this->term_id,
        ];
        return implode(' - ', $titles);
    }

}
