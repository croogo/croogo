<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add(__d('croogo', 'Permissions'), ['plugin' => 'Croogo/Acl', 'controller' => 'Permissions'])
    ->add(__d('croogo', 'Actions'), ['plugin' => 'Croogo/Acl', 'controller' => 'Actions', 'action' => 'index']);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add($aco->id . ': ' . $aco->alias, $this->getRequest()->getRequestTarget());
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
}

$this->assign('form-start', $this->Form->create($aco));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Action'), '#action-main');
$this->end();

$this->append('tab-content');

    echo $this->Form->input('parent_id', [
        'options' => $acos,
        'empty' => true,
        'label' => __d('croogo', 'Parent'),
    ]);
    $this->Form->templates([
        'class' => 'span10',
    ]);
    echo $this->Form->input('alias', [
        'label' => __d('croogo', 'Alias'),
    ]);

    $this->end();
