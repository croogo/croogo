<?php
	$url = Router::url(array(
		'controller' => 'contacts',
		'action' => 'view',
		$contact['Contact']['alias'],
	), true);
	echo __('You have received a new message at: %s', $url) . "\n \n";
	echo __('Name: %s', $message['Message']['name']) . "\n";
	echo __('Email: %s', $message['Message']['email']) . "\n";
	echo __('Subject: %s', $message['Message']['title']) . "\n";
	echo __('IP Address: %s', $_SERVER['REMOTE_ADDR']) . "\n";
	echo __('Message: %s', $message['Message']['body']) . "\n";
?>