<?php
	function rss_transform($item) {
		$name = $item['Comment']['name'];
		if ($item['User']['name']) {
			$name = $item['User']['name'];
		}

		$item['Node']['url'] = array(
			'controller' => 'nodes',
			'action' => 'view',
			'type' => $item['Node']['type'],
			'slug' => $item['Node']['slug'],
		);

		return array(
			'title' => __('Comment on', true) . ' ' . $item['Node']['title'] . ' ' . __('by', true) . ' ' . $name,
			'link' => Router::url($item['Node']['url'], true) . '#comment-' . $item['Comment']['id'],
			'guid' => Router::url($item['Node']['url'], true) . '#comment-' . $item['Comment']['id'],
			'description' => $item['Comment']['body'],
			'pubDate' => $item['Comment']['created'],
			);
	}

	$this->set('items', $rss->items($comments, 'rss_transform'));
?>