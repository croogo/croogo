<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Contacts'), ['controller' => 'contacts', 'action' => 'index']);

if ($this->request->params['action'] == 'edit') {
    $this->Breadcrumbs->add($contact->title, $this->request->getRequestTarget());
}

if ($this->request->params['action'] == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
}

$this->append('form-start', $this->Form->create($contact));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Contact'), '#contact-basic');
echo $this->Croogo->adminTab(__d('croogo', 'Details'), '#contact-details');
echo $this->Croogo->adminTab(__d('croogo', 'Message'), '#contact-message');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('contact-basic') . $this->Form->input('id') . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
        'data-slug' => '#alias',
    ]) . $this->Form->input('alias', [
        'label' => __d('croogo', 'Alias'),
    ]) . $this->Form->input('email', [
        'label' => __d('croogo', 'Email'),
    ]) . $this->Form->input('body', [
        'label' => __d('croogo', 'Body'),
    ]);
echo $this->Html->tabEnd();

echo $this->Html->tabStart('contact-details') . $this->Form->input('name', [
        'label' => __d('croogo', 'Name'),
    ]) . $this->Form->input('position', [
        'label' => __d('croogo', 'Position'),
    ]) . $this->Form->input('address', [
        'label' => __d('croogo', 'Address'),
    ]) . $this->Form->input('address2', [
        'label' => __d('croogo', 'Address2'),
    ]) . $this->Form->input('state', [
        'label' => __d('croogo', 'State'),
    ]) . $this->Form->input('country', [
        'label' => __d('croogo', 'Country'),
    ]) . $this->Form->input('postcode', [
        'label' => __d('croogo', 'Post Code'),
    ]) . $this->Form->input('phone', [
        'label' => __d('croogo', 'Phone'),
    ]) . $this->Form->input('fax', [
        'label' => __d('croogo', 'Fax'),
    ]);
echo $this->Html->tabEnd();

echo $this->Html->tabStart('contact-message') . $this->Form->input('message_status', [
        'label' => __d('croogo', 'Let users leave a message'),
    ]) . $this->Form->input('message_archive', [
        'label' => __d('croogo', 'Save messages in database'),
    ]) . $this->Form->input('message_notify', [
        'label' => __d('croogo', 'Notify by email instantly'),
    ]) . $this->Form->input('message_spam_protection', [
        'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
    ]) . $this->Form->input('message_captcha', [
        'label' => __d('croogo', 'Use captcha? (requires Recaptcha API key)'),
    ]);

echo $this->Html->link(__d('croogo', 'You can manage your API keys here.'), [
    'plugin' => 'Croogo/Settings',
    'controller' => 'Settings',
    'action' => 'prefix',
    'Service',
]);
echo $this->Html->tabEnd();
$this->end();

$this->append('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'contact']);
echo $this->Form->input('status', [
        'label' => __d('croogo', 'Published'),
    ]);
echo $this->Html->endBox();
$this->end();
