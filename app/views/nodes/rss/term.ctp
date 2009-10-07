<?php
    function rss_transform($item) {
        return array(
            'title' => $item['Node']['title'],
            'link' => Router::url($item['Node']['url'], true),
            'guid' => Router::url($item['Node']['url'], true),
            'description' => str_replace('/uploads', Router::url('/uploads', true), $item['Node']['body']),
            'pubDate' => $item['Node']['created'],
            );
    }

    $this->set('items', $rss->items($nodes, 'rss_transform'));
?>