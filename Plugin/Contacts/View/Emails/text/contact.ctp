<?php
	$url = Router::url(array(
		'controller' => 'contacts',
		'action' => 'view',
		$contact['Contact']['alias'],
	), true);
	echo __d('croogo', 'You have received a new message at: %s', $url) . "\n \n";
	echo __d('croogo', 'Name: %s', $message['Message']['name']) . "\n";
	echo __d('croogo', 'Email: %s', $message['Message']['email']) . "\n";
	echo __d('croogo', 'Subject: %s', $message['Message']['title']) . "\n";
	echo __d('croogo', 'IP Address: %s', $_SERVER['REMOTE_ADDR']) . "\n";
	echo __d('croogo', 'Message: %s', $message['Message']['body']) . "\n";
?>