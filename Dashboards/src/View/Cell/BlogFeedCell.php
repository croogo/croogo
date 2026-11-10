<?php

namespace Croogo\Dashboards\View\Cell;

use Cake\Cache\Cache;
use Cake\I18n\Time;
use Cake\Utility\Xml;
use Cake\View\Cell;
use Croogo\Core\Link;

class BlogFeedCell extends Cell
{

    public function dashboard(): void
    {
        $this->set('posts', []);
    }

}
