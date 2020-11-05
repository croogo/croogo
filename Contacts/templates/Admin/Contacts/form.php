<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Contacts\Model\Entity\Contact $contact
 */

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Contacts'), ['controller' => 'Contacts', 'action' => 'index']);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($contact->title), $this->getRequest()->getRequestTarget());
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
}

$this->append('form-start', $this->Form->create($contact));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Contact'), '#contact-basic');
echo $this->Croogo->adminTab(__d('croogo', 'Details'), '#contact-details');
echo $this->Croogo->adminTab(__d('croogo', 'Message'), '#contact-message');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('contact-basic') . $this->Form->control('id') . $this->Form->control('title', [
        'label' => __d('croogo', 'Title'),
        'data-slug' => '#alias',
    ]) . $this->Form->control('alias', [
        'label' => __d('croogo', 'Alias'),
    ]) . $this->Form->control('email', [
        'label' => __d('croogo', 'Email'),
    ]) . $this->Form->control('body', [
        'label' => __d('croogo', 'Body'),
    ]);
    echo $this->Html->tabEnd();

    echo $this->Html->tabStart('contact-details') . $this->Form->control('name', [
        'label' => __d('croogo', 'Name'),
    ]) . $this->Form->control('position', [
        'label' => __d('croogo', 'Position'),
    ]) . $this->Form->control('address', [
        'label' => __d('croogo', 'Address'),
    ]) . $this->Form->control('address2', [
        'label' => __d('croogo', 'Address2'),
    ]) . $this->Form->control('state', [
        'label' => __d('croogo', 'State'),
    ]) . $this->Form->control('country', [
        'label' => __d('croogo', 'Country'),
    ]) . $this->Form->control('postcode', [
        'label' => __d('croogo', 'Post Code'),
    ]) . $this->Form->control('phone', [
        'label' => __d('croogo', 'Phone'),
    ]) . $this->Form->control('fax', [
        'label' => __d('croogo', 'Fax'),
    ]);
    echo $this->Html->tabEnd();

    echo $this->Html->tabStart('contact-message') . $this->Form->control('message_status', [
        'label' => __d('croogo', 'Let users leave a message'),
    ]) . $this->Form->control('message_archive', [
        'label' => __d('croogo', 'Save messages in database'),
    ]) . $this->Form->control('message_notify', [
        'label' => __d('croogo', 'Notify by email instantly'),
    ]) . $this->Form->control('message_spam_protection', [
        'label' => __d('croogo', 'Spam protection (requires Akismet API key)'),
    ]) . $this->Form->control('message_captcha', [
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
    echo $this->Form->control('status', [
        'label' => __d('croogo', 'Published'),
    ]);
    echo $this->Html->endBox();
    $this->end();
