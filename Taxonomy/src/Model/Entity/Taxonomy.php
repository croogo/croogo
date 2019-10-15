<?php

namespace Croogo\Taxonomy\Model\Entity;

use Cake\ORM\Entity;

/**
 * Class Taxonomy
 */
class Taxonomy extends Entity
{

    /**
     * @var array
     */
    protected $_virtual = ['title'];

    /**
     * @param $title
     *
     * @return string
     */
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
