<?php
    $url = Router::url(array(
        'controller' => 'contacts',
        'action' => 'view',
        $contact['Contact']['alias'],
    ), true);
    echo sprintf(__('You have received a new message at: %s', true), $url) . "\n \n";
    echo sprintf(__('Name: %s', true), $message['Message']['name']) . "\n";
    echo sprintf(__('Email: %s', true), $message['Message']['email']) . "\n";
    echo sprintf(__('Subject: %s', true), $message['Message']['title']) . "\n";
    echo sprintf(__('IP Address: %s', true), $_SERVER['REMOTE_ADDR']) . "\n";
    echo sprintf(__('Message: %s', true), $message['Message']['body']) . "\n";
?>