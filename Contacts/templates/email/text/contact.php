<?php
/**
 * @var \App\View\AppView $this
 * @var array $_SERVER
 * @var \Croogo\Contacts\Model\Entity\Contact $contact
 * @var \Croogo\Contacts\Model\Entity\Message $message
 */

use Cake\Routing\Router;

$url = Router::url([
    'controller' => 'Contacts',
    'action' => 'view',
    $contact->alias,
], true);
echo __d('croogo', 'You have received a new message at: %s', $url) . "\n \n";
echo __d('croogo', 'Name: %s', $message->name) . "\n";
echo __d('croogo', 'Email: %s', $message->email) . "\n";
echo __d('croogo', 'Subject: %s', $message->title) . "\n";
echo __d('croogo', 'IP Address: %s', $_SERVER['REMOTE_ADDR']) . "\n";
echo __d('croogo', 'Message: %s', $message->body) . "\n";
