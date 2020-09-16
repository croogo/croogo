<?php

$Url = $this->Url;
$items = $this->Rss->items($nodes->toArray(), function ($item) use ($Url) {
    return [
        'title' => $item->title,
        'link' => $Url->build($item->url->getUrl(), true),
        'guid' => $Url->build($item->url->getUrl(), true),
        'description' => $item->body,
        'pubDate' => $item->publish_start,
    ];
});

$this->set('items', $items);
