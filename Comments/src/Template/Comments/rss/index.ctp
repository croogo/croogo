<?php

use Cake\Core\Configure;
use Cake\Routing\Router;

$channel = [
    'title' => __d('croogo', 'Comments') . ' - ' . Configure::read('Site.title'),
    'description' => Configure::read('Site.tagline'),
];
$this->set('channel', $channel);

function rss_transform($item) {
    $name = $item->name;
    if ($item->user->name) {
        $name = $item->user->name;
    }

    return array(
        'title' => __d('croogo', 'Comment on') . ' ' . $item->node->title . ' ' . __d('croogo', 'by') . ' ' . $name,
        'link' => Router::url($item->node->url->getUrl(), true) . '#comment-' . $item->id,
        'guid' => Router::url($item->node->url->getUrl(), true) . '#comment-' . $item->id,
        'description' => $item->body,
        'pubDate' => $item->created,
    );
}

$this->set('items', $this->Rss->items($comments->toArray(), 'rss_transform'));