<?php

namespace Croogo\Translate\Model\Behavior;

use ArrayObject;
use Cake\Event\Event;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\Behavior\TranslateBehavior as CakeTranslateBehavior;

class TranslateBehavior extends CakeTranslateBehavior
{

    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (empty($data['_locale'])) {
            $data['_locale'] = I18n::defaultLocale();
        }
    }

}
