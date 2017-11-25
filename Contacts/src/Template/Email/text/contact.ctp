<?php
$url = \Cake\Routing\Router::url([
    'controller' => 'contacts',
    'action' => 'view',
    $contact['Contact']['alias'],
], true);
echo __d('croogo', 'You have received a new message at: %s', $url) . "\n \n";
echo __d('croogo', 'Name: %s', $message->name) . "\n";
echo __d('croogo', 'Email: %s', $message->email) . "\n";
echo __d('croogo', 'Subject: %s', $message->title) . "\n";
echo __d('croogo', 'IP Address: %s', $_SERVER['REMOTE_ADDR']) . "\n";
echo __d('croogo', 'Message: %s', $message->body) . "\n";
