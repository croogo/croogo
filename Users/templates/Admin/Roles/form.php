<?php
$this->extend('Croogo/Core./Common/admin_edit');
$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add(__d('croogo', 'Roles'), ['plugin' => 'Croogo/Users', 'controller' => 'Roles', 'action' => 'index']);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($role->title), $this->getRequest()->getRequestTarget());
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
}

$this->assign('form-start', $this->Form->create($role));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Role'), '#role-main');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('role-main');
echo $this->Form->input('title', [
    'label' => __d('croogo', 'Title'),
    'data-slug' => '#alias'
]);
echo $this->Form->input('alias', [
    'label' => __d('croogo', 'Alias'),
]);
echo $this->Html->tabEnd();
$this->end();
