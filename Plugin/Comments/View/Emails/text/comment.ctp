<?php
	echo __d('croogo', 'A new comment has been posted under: %s', $node['Node']['title']) . "\n \n";

    echo Router::url($node['Node']['url'], true) . '#comment-' . $commentId . "\n \n";

    echo __d('croogo', 'Name: %s', $data['Comment']['name']) . "\n";
    echo __d('croogo', 'Email: %s', $data['Comment']['email']) . "\n";
    echo sprintf( __d('croogo', 'Website: %s'), $data['Comment']['website']) . "\n";
    echo __d('croogo', 'IP Address: %s', $data['Comment']['ip']) . "\n \n";
    echo __d('croogo', 'Comment: %s', $data['Comment']['body']) . "\n";
?>