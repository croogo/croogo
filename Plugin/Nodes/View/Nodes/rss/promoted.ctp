<?php

	function rss_transform($item) {
		return array(
			'title' => $item['Node']['title'],
			'link' => Router::url($item['Node']['url'], true),
			'guid' => Router::url($item['Node']['url'], true),
			'description' => $item['Node']['body'],
			'pubDate' => $item['Node']['created'],
		);
	}

	$this->set('items', $this->Rss->items($nodes, 'rss_transform'));
?>