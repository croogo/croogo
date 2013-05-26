<?php
	echo __d('croogo', 'A new comment has been posted under: %s', $node['Node']['title']) . "\n \n";

	echo Router::url($node['Node']['url'], true) . '#comment-' . $commentId . "\n \n";

	echo __d('croogo', 'Name: %s', $data['name']) . "\n";
	echo __d('croogo', 'Email: %s', $data['email']) . "\n";
	echo sprintf( __d('croogo', 'Website: %s'), $data['website']) . "\n";
	echo __d('croogo', 'IP Address: %s', $data['ip']) . "\n";
	echo __d('croogo', 'Comment: %s', $data['body']) . "\n";
?>