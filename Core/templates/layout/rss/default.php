<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $items
 */

use Cake\Core\Configure;

if (!isset($channel)) :
    $channel = [];
endif;

if (!isset($channel['title'])) :
    $channel['title'] = Configure::read('Site.title');
endif;

if (!isset($channel['title'])) :
    $channel['description'] = Configure::read('Site.tagline');
endif;

$channelEl = $this->Rss->channel([], $channel, $items);
echo $this->Rss->document($channelEl);
