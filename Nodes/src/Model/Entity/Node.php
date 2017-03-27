<?php

namespace Croogo\Nodes\Model\Entity;

use Cake\ORM\Behavior\Translate\TranslateTrait;
use Cake\ORM\Entity;

/**
 * @property string type Type of node
 * @property \Croogo\Core\Link url
 */
class Node extends Entity
{

    use TranslateTrait;

}
