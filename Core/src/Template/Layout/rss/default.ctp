<?php

use Cake\Core\Configure;

if (!isset($channel)):
    $channel = [];
endif;

if (!isset($channel['title'])):
    $channel['title'] = $this->fetch('title') . ' - ' . Configure::read('Site.title');
endif;

$channelEl = $this->Rss->channel([], $channel, $items);
echo $this->Rss->document($channelEl);
?>