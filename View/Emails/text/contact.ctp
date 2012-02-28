<?php
	$url = Router::url(array(
		'controller' => 'contacts',
		'action' => 'view',
		$contact['Contact']['alias'],
	), true);
	echo sprintf(__('You have received a new message at: %s'), $url) . "\n \n";
	echo sprintf(__('Name: %s'), $message['Message']['name']) . "\n";
	echo sprintf(__('Email: %s'), $message['Message']['email']) . "\n";
	echo sprintf(__('Subject: %s'), $message['Message']['title']) . "\n";
	echo sprintf(__('IP Address: %s'), $_SERVER['REMOTE_ADDR']) . "\n";
	echo sprintf(__('Message: %s'), $message['Message']['body']) . "\n";
?>