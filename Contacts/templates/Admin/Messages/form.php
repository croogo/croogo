<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Contacts\Model\Entity\Message $message
 */
$this->assign('title', __d('croogo', 'Edit Message'));
$this->extend('/Common/admin_edit');

$this->Breadcrumbs->add(
    __d('croogo', 'Contacts'),
    ['plugin' => 'Croogo/Contacts', 'controller' => 'Contacts', 'action' => 'index']
)
    ->add(
        __d('croogo', 'Messages'),
        ['plugin' => 'Croogo/Contacts', 'controller' => 'Messages', 'action' => 'index']
    );

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($message->title), $this->getRequest()->getRequestTarget());
}

$this->append('form-start', $this->Form->create($message));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Message'), '#message-main');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('message-main') . $this->Form->control('name', [
        'label' => __d('croogo', 'Name'),
    ]) . $this->Form->control('email', [
        'label' => __d('croogo', 'Email'),
    ]) . $this->Form->control('title', [
        'label' => __d('croogo', 'Title'),
    ]) . $this->Form->control('body', [
        'label' => __d('croogo', 'Body'),
    ]) . $this->Form->control('phone', [
        'label' => __d('croogo', 'Phone'),
    ]) . $this->Form->control('address', [
        'label' => __d('croogo', 'Address'),
    ]);
    echo $this->Html->tabEnd();
    $this->end();
