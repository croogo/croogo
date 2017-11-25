<?php

namespace Croogo\Menus\Model\Entity;

use Cake\ORM\Behavior\Translate\TranslateTrait;
use Cake\ORM\Entity;
use Croogo\Acl\Traits\RowLevelAclTrait;

class Menu extends Entity
{

    use RowLevelAclTrait;

    use TranslateTrait;

}
