<?php

namespace Croogo\Blocks\Model\Entity;

use Cake\ORM\Behavior\Translate\TranslateTrait;
use Cake\ORM\Entity;
use Croogo\Acl\Traits\RowLevelAclTrait;

class Block extends Entity
{

    use RowLevelAclTrait;

    use TranslateTrait;

}
