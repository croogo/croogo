<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $Url
 * @var object $item
 * @var object $nodes
 */

use Cake\Core\Configure;

$this->loadHelper('Croogo/Core.Rss');

$channel = [
    'title' => Configure::read('Site.title'),
];

$items = $this->Rss->items($nodes->toArray(), function ($item) use ($Url) {
    $itemUrl = $this->Url->build($item->url->getUrl(), ['fullBase' => true]);
    return [
        'title' => $item->title,
        'link' => $itemUrl,
        'guid' => $itemUrl,
        'description' => $item->body,
        'pubDate' => $item->publish_start,
    ];
});

$this->set(compact('channel', 'items'));
