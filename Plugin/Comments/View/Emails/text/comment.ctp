<?php
	echo __('A new comment has been posted under: %s', $node['Node']['title']) . "\n \n";

	echo Router::url($node['Node']['url'], true) . '#comment-' . $commentId . "\n \n";

	echo __('Name: %s', $data['name']) . "\n";
	echo __('Email: %s', $data['email']) . "\n";
	echo sprintf( __('Website: %s'), $data['website']) . "\n";
	echo __('IP Address: %s', $data['ip']) . "\n";
	echo __('Comment: %s', $data['body']) . "\n";
?>