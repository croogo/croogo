<?php
$this->assign('title', __d('croogo', 'Edit Message'));
$this->extend('/Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Contacts'),
    ['plugin' => 'Croogo/Contacts', 'controller' => 'Contacts', 'action' => 'index'])
    ->add(__d('croogo', 'Messages'),
        ['plugin' => 'Croogo/Contacts', 'controller' => 'Messages', 'action' => 'index']);

if ($this->request->params['action'] == 'edit') {
    $this->Breadcrumbs->add($message->title, $this->request->getRequestTarget());
}

$this->append('form-start', $this->Form->create($message));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Message'), '#message-main');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('message-main') . $this->Form->input('name', [
        'label' => __d('croogo', 'Name'),
    ]) . $this->Form->input('email', [
        'label' => __d('croogo', 'Email'),
    ]) . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
    ]) . $this->Form->input('body', [
        'label' => __d('croogo', 'Body'),
    ]) . $this->Form->input('phone', [
        'label' => __d('croogo', 'Phone'),
    ]) . $this->Form->input('address', [
        'label' => __d('croogo', 'Address'),
    ]);
echo $this->Html->tabEnd();
$this->end();
