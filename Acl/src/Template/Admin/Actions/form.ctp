<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'))
    ->add(__d('croogo', 'Permissions'), array('plugin' => 'Croogo/Acl', 'controller' => 'Permissions'))
    ->add(__d('croogo', 'Actions'), array('plugin' => 'Croogo/Acl', 'controller' => 'Actions', 'action' => 'index'));

if ($this->request->param('action') == 'edit') {
    $this->Breadcrumbs->add($aco->id . ': ' . $aco->alias, $this->request->getRequestTarget());
}

if ($this->request->param('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
}

$this->assign('form-start', $this->Form->create($aco));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Action'), '#action-main');
$this->end();

$this->append('tab-content');

    echo $this->Form->input('parent_id', array(
        'options' => $acos,
        'empty' => true,
        'label' => __d('croogo', 'Parent'),
    ));
    $this->Form->templates(array(
        'class' => 'span10',
    ));
    echo $this->Form->input('alias', array(
        'label' => __d('croogo', 'Alias'),
    ));

$this->end();
